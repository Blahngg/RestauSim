<div>
    <style>
        .table{
            cursor: pointer;
        }
    </style>
    <a href="{{ route('floorplan.create') }}"><button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Create</button></a>
    
    <div class="flex">
        <div>
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

    <script>
        function bindTables(){
            
        }
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
    </script>
</div>
