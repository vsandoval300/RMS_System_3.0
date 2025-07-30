<div class="text-sm text-gray-300 space-y-2">
    <h4 class="text-sm font-semibold mb-4">Document Details</h4>

    @php
        use Carbon\Carbon;

        $coverageDays = null;

        if (!empty($inceptionDate) && !empty($expirationDate)) {
            try {
                $start = Carbon::parse($inceptionDate);
                $end = Carbon::parse($expirationDate);
                $coverageDays = $start->diffInDays($end);
            } catch (\Exception $e) {
                $coverageDays = null;
            }
        }
    @endphp

    <table class="w-full text-sm border-separate border-spacing-y-1">
        <tbody>
            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-right font-medium text-gray-400 w-1/4">Id document:</td>
                <td class="px-2 py-1 w-1/4">{{ $id ?? '-' }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-400 w-1/4">Creation date:</td>
                <td class="px-2 py-1 w-1/4">
                    {{ $createdAt ? Carbon::parse($createdAt)->format('d/m/Y') : '-' }}
                </td>
            </tr>
            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-right font-medium text-gray-400">Document type:</td>
                <td class="px-2 py-1">{{ $documentType ?? '-' }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Period:</td>
                <td class="px-2 py-1">
                    {{ $inceptionDate ? Carbon::parse($inceptionDate)->format('d/m/Y') : '-' }}
                    to
                    {{ $expirationDate ? Carbon::parse($expirationDate)->format('d/m/Y') : '-' }}
                </td>
            </tr>
            <tr class="border-b border-gray-600">
                <td class="px-2 py-1 text-right font-medium text-gray-400">Premium type:</td>
                <td class="px-2 py-1">{{ $premiumType ?? '-' }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Coverage days:</td>
                <td class="px-2 py-1">{{ $coverageDays ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>






