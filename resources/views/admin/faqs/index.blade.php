<x-layout title="FAQ" title-icon="help-circle" subtitle="Manage the frequently asked questions salespeople see">

    <div class="flex justify-end">
        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-faq')" class="btn-primary shrink-0">
            <x-icon name="plus" class="h-4 w-4" />
            Add FAQ
        </button>
    </div>

    <div class="mt-6 card">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-800">All FAQs</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $faqs->count() }} total question{{ $faqs->count() === 1 ? '' : 's' }}</p>
        </div>

        @if ($faqs->count())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3 font-semibold">Question</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold">Sort Order</th>
                            <th class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($faqs as $faq)
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                            <x-icon name="help-circle" class="h-5 w-5" />
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-800">{{ $faq->question }}</p>
                                            <p class="mt-0.5 max-w-md truncate text-xs text-slate-400">{{ $faq->answer }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    @if ($faq->is_published)
                                        <span class="badge badge-green">
                                            <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                            Published
                                        </span>
                                    @else
                                        <span class="badge badge-slate">
                                            <x-icon name="document" class="h-3.5 w-3.5" />
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-slate-500">{{ $faq->sort_order }}</td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-faq-{{ $faq->id }}')" title="Edit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-600 transition hover:bg-violet-100">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </button>

                                        <form method="POST" action="{{ route('admin.faqs.publish.toggle', $faq) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_published" value="{{ $faq->is_published ? '0' : '1' }}">
                                            <button type="submit" title="{{ $faq->is_published ? 'Move to Draft' : 'Publish' }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-green-200 bg-green-50 text-green-600 transition hover:bg-green-100">
                                                <x-icon name="{{ $faq->is_published ? 'eye-off' : 'check-circle' }}" class="h-4 w-4" />
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" x-data="" x-on:submit.prevent="$dispatch('confirm-action', { message: 'Delete this FAQ?', target: $el })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <x-modal name="edit-faq-{{ $faq->id }}" :show="false" max-width="lg">
                                @include('admin.faqs._form', ['faq' => $faq])
                            </x-modal>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50">
                    <x-icon name="help-circle" class="h-7 w-7 text-brand-600" />
                </div>
                <h3 class="mt-4 font-bold text-slate-800">No FAQs found</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-slate-400">Click "Add FAQ" to create the first one.</p>
            </div>
        @endif
    </div>

    <x-modal name="add-faq" :show="$errors->isNotEmpty()" max-width="lg">
        @include('admin.faqs._form', ['faq' => null])
    </x-modal>

</x-layout>
