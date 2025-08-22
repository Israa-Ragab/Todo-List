<div>
    @include('livewire.includes.create')

    <!-- Tasks -->
    <ul class="list-group mb-3">

        @foreach($tasks as $task)
        @include('livewire.includes.todo_table')
        @endforeach

    </ul>

    <div class="d-flex justify-content-center mt-3">
        {{ $tasks->links() }}
    </div>
    <!-- Clear All -->
    @if(count($tasks) > 0 )
    <div class="text-end">
        <button wire:click="confirmDeleteAll" class="btn btn-danger">
            <i class="bi bi-trash-fill me-1"></i> Clear All
        </button>
    </div>
    @endif

</div>
