# RMS Platform 4 — CLAUDE.md

## Project Overview

**RMS Platform 4** is a **Reinsurance Management System** built with Laravel 12 + Filament v4.
It manages reinsurance contracts (businesses), operative documents, and the financial transaction lifecycle between cedants, intermediaries, and reinsurers.

Admin panel available at `/admin`. Brand name: `RMS-System`.

---

## Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 / PHP 8.2+ |
| Admin Panel | Filament v4 (`filament/filament ~4.0`) |
| RBAC | Filament Shield v4 (`bezhansalleh/filament-shield ^4.2`) |
| PDF | DomPDF (`dompdf/dompdf ^2.0`) |
| Excel | Maatwebsite Excel (`maatwebsite/excel ^3.1`) |
| File Storage | AWS S3 + FTP via Flysystem |
| Monitoring | Laravel Nightwatch |
| Local Environment | XAMPP / MySQL |
| Frontend | Vite + Livewire (via Filament) |

---

## Domain Model

### Core hierarchy

```
Business (businesses)
  └── OperativeDoc (operative_docs)       — insurance policy document
        └── Transaction (transactions)    — financial installment
              └── TransactionLog (transaction_logs)  — ledger line items per party
```

### Key entities

- **Business** — Insurance contract. Has `premium_type` (Fixed / Estimated / Declared), `reinsurance_type`, `risk_covered`, `business_type`, `approval_status`, `business_lifecycle_status`. PK: `business_code` (string).
- **OperativeDoc** — Operative document linked to a Business. Has `inception_date`, `expiration_date`, `rep_date`, `af_mf`, `roe_fs`. UUID PK.
- **Transaction** — Financial installment within an OperativeDoc. Has `proportion`, `exch_rate`, `due_date`, `amount`, `transaction_type_id`, `transaction_status_id`, `remmitance_code`. UUID PK. Soft-deleted.
- **TransactionLog** — Individual ledger line per party/deduction within a Transaction. Has `deduction_type`, `from_entity`, `to_entity`, `gross_amount`, `gross_amount_calc`, `commission_discount`, `banking_fee`, `net_amount`, `status` (Pending / In process / Completed), `sent_date`, `received_date`. UUID PK.
- **CostScheme / CostNodex** — Pricing structure defining how premium flows through parties (source → destination) with deductions and commissions.
- **Reinsurer / Partner / Holding** — Counterparty entities.
- **Treaty / TreatyDoc** — Reinsurance treaty documents.
- **Client / Producer / Company** — Insured-side entities.

---

## Key Patterns

### UUID Primary Keys
`Transaction`, `TransactionLog`, `OperativeDoc`, `Business (business_code)` all use string PKs with `$incrementing = false`. UUIDs auto-generated in `booted()` → `creating`.

### Auto-index per document
On `Transaction::creating`, a consecutive `index` is calculated via `MAX(index)` for the same `op_document_id`.

### Auto Transaction Log Generation
When a `Transaction` is **created**, its `booted()` `created` hook triggers `TransactionLogsPreviewService::build()`, which:
1. Loads the OperativeDoc with its CostScheme → CostNodex tree.
2. Sums premiums from `businessdoc_insureds` grouped by `cscheme_id`.
3. Iterates CostNodex nodes to compute gross amounts, commissions, banking fees, net amounts per party.
4. Bulk-inserts `TransactionLog` rows.

**Entry point:** `app/Services/TransactionLogsPreviewService.php`
**Builder wrapper:** `app/Services/TransactionLogsBuilderService.php`

### Transaction Lifecycle Status
Computed dynamically from log statuses via `Transaction::resolveTransactionStatus()`:
- `PENDING` — no logs or all pending
- `IN_PROCESS` — at least one log is "In process" or "Completed" (but not last)
- `COMPLETED` — last log is "Completed"

Progress percentage via `Transaction::lifecycleProgressPercentage()` → 0–100.

Enum: `app/Enums/TransactionLifecycleStatus.php` (values: `1`, `2`, `3`).

### Audit Trail
`HasAuditLogs` trait mixed into all major models. Fires `created`/`updated`/`deleted` events writing to a polymorphic `audit_logs` table. Human-readable label override available (e.g., `DOC123-TX01`).

### RBAC
Filament Shield generates permissions from policies. Each model has its own policy in `app/Policies/`. `HasPageShield` trait used on custom pages.

### Soft Deletes
All core models use `SoftDeletes`. On `Transaction::deleted`, related logs are soft-deleted and sibling transactions are re-indexed. On `restored`, logs are restored too.

---

## Filament Panel Structure

```
app/Filament/
├── Clusters/
│   └── Resources.php                  — sidebar grouping cluster
├── Pages/
│   ├── UnderwrittenDashboard.php
│   └── UsersDashboard.php
├── Resources/
│   ├── Businesses/
│   ├── Clients/
│   ├── CostSchemes/
│   ├── Holdings/
│   ├── Reinsurers/
│   ├── Transactions/
│   │   ├── TransactionResource.php
│   │   ├── Pages/
│   │   │   ├── ListTransactions.php
│   │   │   ├── CreateTransaction.php
│   │   │   ├── EditTransaction.php
│   │   │   └── ViewTransaction.php
│   │   ├── RelationManagers/
│   │   │   ├── LogsRelationManager.php
│   │   │   └── SupportsRelationManager.php
│   │   └── Widgets/
│   │       └── TransactionStatsOverview.php
│   ├── TransactionLogs/
│   ├── Treaties/
│   └── ... (30+ resources total)
├── Underwritten/
│   ├── Resources/
│   └── Widgets/
└── User/
```

Navigation groups: `Resources`, `Banks`, `Customers`, `Compliance`, `Reinsurers`, `Underwritten`, `Transactions`, `Filament Shield`.

---

## Services

| Service | Purpose |
|---|---|
| `TransactionLogsPreviewService` | Core calculation engine — builds TransactionLog rows from CostScheme/CostNodex tree. Used on preview and on transaction creation. |
| `TransactionLogsBuilderService` | Wrapper around PreviewService for building insert payload for existing transactions. |
| `OperativeDocSummaryV2Service` | Generates operative document summary. |
| `PremiumForMonthService` | Premium calculation per month. |
| `PremiumForPeriodService` | Premium calculation per period. |

---

## Jobs & Observers

- `GenerateOperativeDocsReport` — Queue job for report generation.
- `NotifyReportReady` — Notifies user when report is ready.
- `OperativeDocObserver` — Observes OperativeDoc lifecycle events.

---

## Exports

- `TransactionsReportExport` — Excel export via Maatwebsite Excel.

---

## Planned Feature: Estimated / Declared Premium Recalculations

**Context:** When `Business.premium_type` is `Estimated` or `Declared`, transactions are created with estimated amounts. As real bordereaux arrive quarterly, each installment must be recalculated to the actual net amount.

**Planned approach:**
- New table: `transaction_recalculations`
  - `id`, `transaction_id`, `recalculation_no`, `bordereaux_reference`, `previous_amount`, `reported_premium`, `reported_claims`, `new_amount`, `evidence_path`, `notes`, `created_by`, `created_at`, `updated_at`, `deleted_at`
- Modal triggered from "Recalculate Lifecycle" button on EditTransaction (visible only when premium_type is Estimated or Declared).
- On confirmation: save recalculation record → update `transactions.amount` → delete and regenerate `transaction_logs` via `TransactionLogsBuilderService`.
- New "Recalculations" tab on ViewTransaction to show history.

---

## Active Branch

`upgrade/filament-v4` — Ongoing Filament v4 upgrade. Changes concentrated in:
- `app/Filament/Resources/Transactions/` (all pages, relation managers, resource, widgets)
- `app/Models/Transaction.php`, `TransactionLog.php`
- `app/Services/TransactionLogsPreviewService.php`, `TransactionLogsBuilderService.php`
- New blade components: `transaction-logs-table`, `transaction-progress-bar`, `transaction-progress-bar-inline`, `transaction-progress-column`, `transaction-lifecycle-modal`
- New migration: `alter_generated_columns_on_transaction_logs_table`

---

## Common Commands

```bash
# Start dev server (all processes)
composer dev

# Run tests
composer test

# Filament upgrade after composer update
php artisan filament:upgrade

# Generate Shield permissions
php artisan shield:generate --all

# Clear config
php artisan config:clear
```
