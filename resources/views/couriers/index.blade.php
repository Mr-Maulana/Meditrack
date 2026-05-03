@extends('layouts.app')

@section('title', 'Manajemen Kurir')
@section('page-title', 'Kurir')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-tni-800">Daftar Kurir</h2>
        <p class="text-sm text-gray-600 mt-1">Kelola data kurir pengantar obat Rumkit TK III IM 07.01 Lhokseumawe</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('couriers.performance-index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow flex items-center transition">
            <i class="fas fa-chart-line mr-2"></i> Laporan Performa
        </a>
        <a href="{{ route('couriers.create') }}" class="bg-tni-600 hover:bg-tni-700 text-white px-4 py-2 rounded-lg shadow flex items-center transition">
            <i class="fas fa-plus mr-2"></i> Tambah Kurir
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama & Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung Sejak</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($couriers as $index => $courier)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $couriers->firstItem() + $index }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-tni-100 rounded-full flex items-center justify-center text-tni-600 font-bold">
                                {{ substr($courier->name, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $courier->name }}</div>
                                <div class="text-sm text-gray-500">{{ $courier->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $courier->phone ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $courier->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('couriers.performance', $courier) }}" class="text-blue-600 hover:text-blue-900 mx-1" title="Performa"><i class="fas fa-chart-bar"></i></a>
                        <a href="{{ route('couriers.show', $courier) }}" class="text-tni-600 hover:text-tni-900 mx-1" title="Detail"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('couriers.edit', $courier) }}" class="text-yellow-600 hover:text-yellow-900 mx-1" title="Edit"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('couriers.destroy', $courier) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kurir ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 mx-1" title="Hapus"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data kurir</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($couriers->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $couriers->links() }}
    </div>
    @endif
</div>
@endsection
