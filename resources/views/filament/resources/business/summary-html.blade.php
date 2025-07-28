<div class="prose dark:prose-invert max-w-none">
    <h4 class="text-base font-semibold mb-4">Document Details</h4>

    <table class="w-full text-sm border-separate border-spacing-y-1">
        <tbody>
            <tr>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Id document:</td>
                <td class="px-2 py-1">{{ $id }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Creation date:</td>
                <td class="px-2 py-1">{{ \Carbon\Carbon::parse($createdAt)->format('d/m/y') }}</td>
            </tr>
            <tr>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Document type:</td>
                <td class="px-2 py-1">{{ $documentType }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Period:</td>
                <td class="px-2 py-1">
                    {{ \Carbon\Carbon::parse($inceptionDate)->format('d/m/Y') }}
                    to
                    {{ \Carbon\Carbon::parse($expirationDate)->format('d/m/Y') }}
                </td>
            </tr>
            <tr>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Premium type:</td>
                <td class="px-2 py-1">{{ $premiumType ?? '-' }}</td>
                <td class="px-2 py-1 text-right font-medium text-gray-400">Coverage days:</td>
                <td class="px-2 py-1">
                    {{ \Carbon\Carbon::parse($inceptionDate)->diffInDays(\Carbon\Carbon::parse($expirationDate)) + 1 }}
                </td>
            </tr>
        </tbody>
    </table>

    <hr class="my-4" />

    <h4 class="text-base font-semibold mt-4">Insureds</h4>
</div>








