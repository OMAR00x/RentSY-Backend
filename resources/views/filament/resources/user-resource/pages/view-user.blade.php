<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">المعلومات الشخصية</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">الاسم الكامل</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $record->first_name }} {{ $record->last_name }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">رقم الهاتف</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $record->phone }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">تاريخ الميلاد</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $record->birthdate?->format('Y-m-d') ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">تاريخ التسجيل</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $record->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">النوع</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $record->role === 'owner' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                            {{ $record->role === 'owner' ? 'مؤجر' : 'مستأجر' }}
                        </span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">الحالة</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold
                            {{ $record->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                            {{ $record->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $record->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                            {{ $record->status === 'pending' ? 'معلق' : ($record->status === 'approved' ? 'موافق' : 'مرفوض') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الصور والمستندات</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $avatar = $record->images()->where('type', 'avatar')->first();
                        $idFront = $record->images()->where('type', 'id_front')->first();
                        $idBack = $record->images()->where('type', 'id_back')->first();
                    @endphp

                    <div class="space-y-2">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">الصورة الشخصية</p>
                        @if($avatar)
                            <img src="{{ asset('storage/' . $avatar->url) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700" alt="الصورة">
                        @else
                            <div class="w-full h-32 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                <span class="text-gray-400 text-xs">لا توجد صورة</span>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">هوية (أمامية)</p>
                        @if($idFront)
                            <img src="{{ asset('storage/' . $idFront->url) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700" alt="هوية">
                        @else
                            <div class="w-full h-32 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                <span class="text-gray-400 text-xs">لا توجد صورة</span>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">هوية (خلفية)</p>
                        @if($idBack)
                            <img src="{{ asset('storage/' . $idBack->url) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700" alt="هوية">
                        @else
                            <div class="w-full h-32 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                <span class="text-gray-400 text-xs">لا توجد صورة</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
