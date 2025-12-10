<x-app-layout>
  @if($errors->any())
    <ul>
      @foreach ($errors->all() as $message)
        <li class=" text-red-500">
          {{ $message }}
        </li>
      @endforeach
    </ul>
  @endif
  <form action="{{ route('floorplan.store') }}" method="POST" class="w-1/2 m-10" enctype="multipart/form-data">
    @csrf
    <h3 class="text-3xl font-bold dark:text-white text-center">Upload Floor Plan</h3>
    <div class="p-5">
      <x-form.floating-input
        label="Name"
        id="name"
        type="text"
        />
      <div class=" pt-5">
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload file</label>
        <input name="filepath" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" aria-describedby="file_input_help" id="file_input" type="file">
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">SVG</p>
      </div>
      <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 mt-5">Submit</button>
    </div>
  </form>
  {{-- document.querySelector('[data-cell-id="table-1"]') --}}
  {{-- test other floor plans creator --}}
</x-app-layout>