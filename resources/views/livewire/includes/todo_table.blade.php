<li class="list-group-item d-flex justify-content-between align-items-center 
            {{ $task->is_completed ? 'list-group-item-success' : '' }}">

    <div>
        <input type="checkbox" wire:click="toggleComplete({{ $task->id }})" {{ $task->is_completed ? 'checked' : '' }}>
        <span class="{{ $task->is_completed ? 'text-decoration-line-through' : '' }}">
            {{ $task->content }}
        </span>

        @if($task->priority == "1")
        <span class="badge bg-warning ms-2">Important</span>
        @elseif($task->priority == "2")
        <span class="badge bg-danger ms-2">Very Important</span>
        @endif


        <div class="text-muted small mt-1">
            {{ $task->created_at->format('d M Y - h:i A') }}
        </div>

        @if($editTaskId === $task->id)
        <form wire:submit.prevent="updateTask">
            <div class="input-group mt-2">
                <input type="text" class="form-control" wire:model="editContent">
                <button class="btn btn-warning btn-sm" type="submit">Save</button>
                <button wire:click="cancelEdit" type="button" class="btn btn-secondary btn-sm">Cancel</button>
            </div>
            @error('editContent') <span class="text-danger">{{ $message }}</span> @enderror
        </form>
        @endif

    </div>
    
    <div>
        <button wire:click="toggleComplete({{ $task->id }})" class="btn btn-sm btn-success me-1 {{ $task->is_completed ? 'disabled': '' }}">
            complete
        </button>
        <button wire:click="taskEdit({{ $task->id }})" class="btn btn-sm btn-warning me-1">
            <i class="bi bi-pencil"></i>
        </button>
        <button wire:click="confirmDelete({{ $task->id }})" class="btn btn-sm btn-danger">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</li>
