<?php

namespace App\Http\Livewire;

use App\Models\Task;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class TodoList extends Component
{
    use LivewireAlert;

    public $name;
    public $priority;
    public $search;
    public $editTaskId = null;
    public $editContent;
    public $deleteId;

    protected $listeners = [
        'deleteTask' => 'deleteTask',
        'clearAll' => 'clearAll',
    ];

    private function showAlert($type, $message)
    {
        $this->alert($type, $message, [
            'position' => 'top',
            'timer' => 4000,
            'toast' => true,
        ]);
    }

    public function createTask()
    {

        try {

            $validate = $this->validate([
                'name' => 'required|min:3|max:50',
                'priority' => 'nullable|in:0,1,2',
            ]);
            
            $priority = $validate['priority'] ?? 0;

            Task::create([
                'content' => $validate['name'],
                'priority' => $priority,
            ]);

    
            $this->reset('name', 'priority');

            $this->showAlert('success', 'Task Created Successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessage = collect($e->validator->errors()->all())->first();

            $this->showAlert('error', $errorMessage);
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->alert('error', 'Are you sure?', [
            'position' => 'center',
            'toast' => true,
            'showConfirmButton' => true,
            'confirmButtonText' => ' Yes, delete it!',
            'confirmButtonColor' => '#d33',
            'onConfirmed' => "deleteTask",
            'showCancelButton' => true,
            'cancelButtonText' => 'Cancel',
            'cancelButtonColor' => '#3085d6',
            'timer' => null,
        ]);
    }

    public function deleteTask()
    {
        try {

            $task = Task::findOrFail($this->deleteId);
            $task->delete();

            $this->showAlert('success', 'Task Deleted Successfully');
        
        } catch (\Throwable $th) {

            $this->showAlert('error', 'Failed To Delete Task!');
        
        }
    }

    public function taskEdit($taskId)
    {
        $task = Task::findOrFail($taskId);
        $this->editTaskId = $task->id;
        $this->editContent = $task->content;
    }

    public function cancelEdit()
    {
        $this->reset(['editTaskId', 'editContent']);
    }

    public function updateTask()
    {

        $this->validate([
            'editContent' => 'required|min:3|max:50',
        ]);
        $task = Task::findOrFail($this->editTaskId);
        $task->update(['content' => $this->editContent]);
        $this->cancelEdit();
        $this->showAlert('success', ' Task Updated Successfully');
    }

    public function toggleComplete($id)
    {
        $task = Task::find($id);
        if ($task) {
            $task['is_completed'] = $task['is_completed'] ? 0 : 1;
            $task->save();
            $this->showAlert('success', $task->is_completed ? '✅ Task Completed' : 'Task Marked Incomplete');
        }
    }

    public function confirmDeleteAll()
    {

        $this->alert('error', 'Are you sure you want to delete all tasks? This action cannot be undone!', [
            'position' => 'center',
            'toast' => true,
            'showConfirmButton' => true,
            'confirmButtonText' => ' Yes, delete All',
            'confirmButtonColor' => '#d33',
            'onConfirmed' => "clearAll",
            'showCancelButton' => true,
            'cancelButtonText' => 'Cancel',
            'cancelButtonColor' => '#3085d6',
            'timer' => null,
        ]);
    }

    public function clearAll()
    {
        try {
            Task::query()->delete();
            $this->showAlert('success', 'All Tasks Deleted Successfully');
        } catch (\Throwable $th) {
            $this->showAlert('error', 'Failed To Delete Tasks!');
        }
    }

    public function render()
    {

        $tasks = Task::latest()
            ->when($this->search, fn($q) => $q->where('content', 'like', "%{$this->search}%"))
            ->paginate(5);
        return view('livewire.todo-list', ['tasks' => $tasks]);
    }
}
