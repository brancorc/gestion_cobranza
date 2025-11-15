<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nuevo Lote
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">
                <form method="POST" action="{{ route('lots.store') }}">
                    @csrf
                    
                    <!-- Header del formulario -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Datos del Lote
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Complete la información requerida para registrar el nuevo lote</p>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Socio (Opcional) -->
                            <div class="group">
                                <x-input-label for="owner_id" class="flex items-center text-gray-700 font-medium mb-2">
                                    Socio Propietario
                                    <span class="ml-2 text-xs text-gray-500 font-normal">(Opcional)</span>
                                </x-input-label>
                                <select id="owner_id" name="owner_id" 
                                        class="select2 mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-2.5">
                                    <option value="">-- Sin asignar o buscar socio --</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" @selected(old('owner_id') == $owner->id)>{{ $owner->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('owner_id')" class="mt-2" />
                            </div>

                            <!-- Cliente (Opcional, con búsqueda) -->
                            <div class="group">
                                <x-input-label for="client_id" class="flex items-center text-gray-700 font-medium mb-2">
                                    Asignar a Cliente
                                    <span class="ml-2 text-xs text-gray-500 font-normal">(Opcional)</span>
                                </x-input-label>
                                <select id="client_id" name="client_id" 
                                        class="select2 mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 py-2.5">
                                    <option value="">-- Sin asignar o buscar cliente --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                            </div>
                            
                            <!-- Manzana -->
                            <div class="group">
                                <x-input-label for="block_number" class="flex items-center text-gray-700 font-medium mb-2">
                                    Número de Manzana
                                    <span class="ml-1 text-red-500">*</span>
                                </x-input-label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <x-text-input id="block_number" name="block_number" 
                                                  class="mt-1 block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" 
                                                  :value="old('block_number')" 
                                                  placeholder="Ej: 1" 
                                                  required />
                                </div>
                                <x-input-error :messages="$errors->get('block_number')" class="mt-2" />
                            </div>

                            <!-- Lote -->
                            <div class="group">
                                <x-input-label for="lot_number" class="flex items-center text-gray-700 font-medium mb-2">
                                    Número de Lote
                                    <span class="ml-1 text-red-500">*</span>
                                </x-input-label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                        </svg>
                                    </div>
                                    <x-text-input id="lot_number" name="lot_number" 
                                                  class="mt-1 block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" 
                                                  :value="old('lot_number')" 
                                                  placeholder="Ej: 15" 
                                                  required />
                                </div>
                                <x-input-error :messages="$errors->get('lot_number')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Nota informativa -->
                        <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Los campos marcados con <span class="text-red-500 font-semibold">*</span> son obligatorios
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer con botones -->
                    <div class="flex items-center justify-end gap-4 bg-gray-50 px-8 py-5 border-t border-gray-200">
                        <a href="{{ route('lots.index') }}" 
                           class="inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </a>
                        <x-primary-button class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Crear Lote
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Select2 para el socio propietario
            $('#owner_id').select2({
                theme: "classic",
                placeholder: "Selecciona o busca un socio",
                allowClear: true,
                width: '100%'
            });

            // Select2 para el cliente
            $('#client_id').select2({
                theme: "classic",
                placeholder: "Selecciona o busca un cliente",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    @endpush
</x-app-layout>