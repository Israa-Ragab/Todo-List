<!-- Input Add Task -->
<div class="input-group mb-3">
    <input wire:model="name" type="text" class="form-control" placeholder="✍️ Add a task...">
  
    <select wire:model="priority" class="form-select" style="max-width: 150px;">
        <option value="">Priority</option>
        <option value="1">Important</option>
        <option value="2">Very Important</option>
        <option value="0">Normal</option>
    </select>
    <button wire:click="createTask" class="btn btn-primary" id="add-btn">
        <i class="bi bi-plus-circle"></i> Add
    </button>
</div>


<!-- Input Search -->
<div class="input-group mb-3">
    <input wire:model.live="search" type="search" class="form-control" placeholder="🔍 Search tasks...">
</div>
