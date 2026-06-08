@extends('layouts.' . $route)

@section('title')
    {{ translate('Edit Shipping Class') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Edit Shipping Class') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route($route . '.shipping-classes.index') }}" class="font-bold ">{{ translate('Class') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Edit Shipping Class') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <form action="{{ route($route . '.shipping-classes.update', $shippingClass->id) }}" method="POST"
        class="shipping-class-form">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-12 gap-3">
            <div class="md:col-span-6 xl:col-span-7 md:row-span-2">
                {{-- code --}}
                <div class="card">
                    <div class="card__title flex justify-between items-center">
                        {{ translate('General Informations') }}
                    </div>

                    <div class="card__content">
                        <div class="space-y-3">

                            <x-backend.inputs.text label="Shipping Class Name" name="name" placeholder="Class Name"
                                value="{{ old('name', $shippingClass->name) }}" />

                            <x-backend.inputs.textarea name="description" label="Description"
                                value="{{ old('description', $shippingClass->description) }}"
                                placeholder='Describe The Class...' :isRequired="false" />

                            <div class="group relative !mt-4" tabindex="0">
                                <x-backend.inputs.text label="Assign Products" class="shipping-class-product-search-input"
                                    name="" value="" placeholder="Select Products By Name" :isRequired="false" />

                                <div
                                    class="shipping-class-product-search absolute z-[1] bg-background rounded-md right-0 top-[calc(100%)] w-full min-w-[300px] min-h-[150px] shadow max-h-[350px] overflow-y-auto p-[2px] divide-y divide-[#78787829] hidden group-focus-within:block transition-all duration-300">

                                    <div class="block py-4 px-6 bg-primary/[.03] text-muted font-bold">
                                        <span class="uppercase">{{ translate('Search Results') }}</span>
                                    </div>

                                    <div class="divide-y divide-[#78787829] shipping-class-product-search-results">

                                    </div>
                                </div>


                            </div>

                            {{-- submit button --}}
                            <div class="flex justify-end mt-6">
                                <x-backend.inputs.button buttonText="Update Class" type="submit" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="md:col-span-6 xl:col-span-5 md:row-span-2">
                {{-- code --}}
                <div class="card">
                    <div class="card__title flex justify-between items-center">
                        {{ translate('Assigned Products') }}
                    </div>

                    <div class="card__content">
                        <table class="shipping-class-table w-full">
                            <tbody class="shipping-class-selected-product-tbody">
                                @forelse ($shippingClass->products as $product)
                                    <tr
                                        class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">
                                        <td class="text-sm text-foreground font-light py-4  tr-name">
                                            <input type="hidden" name="selectedProductIds[]" value="{{ $product->id }}">
                                            {{ $product->collectTranslation('name') }}
                                        </td>
                                        <td class="py-4 whitespace-nowrap text-sm font-medium text-foreground">
                                            <button type="button" class="text-md flex items-center justify-center"
                                                onclick="removeOrderItem(this)">
                                                <i class="fa-regular fa-trash-can text-red-500"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty

                                    <tr class="empty-tr">
                                        <td class="text-center text-red-500">No products assigned!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <x-backend.inc.shipping-class-scripts />
@endsection
