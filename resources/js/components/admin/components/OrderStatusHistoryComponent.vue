<template>
    <div class="db-card p-4" v-if="history.length > 0">
        <h4 class="text-base font-medium mb-4">{{ $t('label.status_history') }}</h4>

        <ul class="flex flex-col gap-4">
            <li v-for="entry in history" :key="entry.id" class="flex gap-3">
                <div class="flex flex-col items-center pt-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary flex-shrink-0"></span>
                    <span class="w-px flex-1 bg-[#E9E9E9]"></span>
                </div>
                <div class="pb-1">
                    <p class="text-xs text-heading font-medium">
                        <template v-if="entry.from_status_name">
                            {{ entry.from_status_name }} &rarr; {{ entry.to_status_name }}
                        </template>
                        <template v-else>
                            {{ $t('label.order_placed') }} ({{ entry.to_status_name }})
                        </template>
                    </p>
                    <p class="text-[11px] text-gray-500 mt-0.5">
                        {{ $t('label.changed_by') }}:
                        {{ entry.changed_by_name || $t('label.system') }}
                        <span v-if="entry.changed_by_source === 'customer'"> ({{ $t('label.customer') }})</span>
                        &nbsp;&middot;&nbsp;
                        {{ entry.changed_at }}
                    </p>
                    <p class="text-[11px] text-gray-500 mt-0.5" v-if="entry.reason">
                        {{ $t('label.reason') }}: {{ entry.reason }}
                    </p>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "OrderStatusHistoryComponent",
    props: {
        history: {
            type: Array,
            default: () => [],
        },
    },
};
</script>
