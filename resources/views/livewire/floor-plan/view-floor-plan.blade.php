<div>
    <style>
        .table{
            cursor: pointer;
        }
    </style>
    <a href="{{ route('floorplan.create') }}"><button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Create</button></a>
    
    <div class="flex">
        <div
            x-data
            @click="
                let table = $event.target.closest('[data-cell-id]');

                while (table) {
                    if (/^table-/.test(table.dataset.cellId)) {
                        break; // found a matching element
                    }
                    // climb to the parent and try again
                    table = table.parentNode.closest('[data-cell-id]');
                }

                if (!table) return;

                const id = table.id || table.dataset.cellId;
                $wire.toggleTable(id);
            "
        >
            {!! file_get_contents(storage_path('app/public/' . $floorplan->filepath)) !!}
        </div>
        @if($showSideBar)
            <aside>
               <h3 class="text-3xl font-bold dark:text-white">{{ $currentTable->table_code }}</h3>
               <a href="{{ route('order.create', $currentTable) }}">
                   <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Add Order</button>
               </a>
            </aside>
        @endif
    </div>

    @teleport('head')
        <style>
            [data-cell-id^="table-"] {
                cursor: pointer;  /* shows a hand pointer */
            }

            [data-cell-id^="table-"] rect,
            [data-cell-id^="table-"] path {
                transition: fill 0.2s;
            }

            [data-cell-id^="table-"]:hover rect,
            [data-cell-id^="table-"]:hover path {
                fill: #93c5fd; /* light blue on hover */
            }

            [data-cell-id^="table-"] rect {
                stroke: #1e40af; /* dark blue border */
                stroke-width: 2;
            }
        </style>
    @endteleport

    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {

            const tables = document.querySelectorAll('.table');

            console.log(tables);

            tables.forEach(el => {
                let id = el.id || el.getAttribute('data-cell-id');

                el.addEventListener('click', () => {
                    console.log(`${id} clicked`);
                    @this.call('toggleTable', id)
                });
            });
        });

        document.addEventListener('livewire:navigated', () => {

            const tables = document.querySelectorAll('.table');

            console.log(tables);

            tables.forEach(el => {
                let id = el.id || el.getAttribute('data-cell-id');

                el.addEventListener('click', () => {
                    console.log(`${id} clicked`);
                    $wire.toggleTable(id);
                });
            });
        });
    </script> --}}
</div>
