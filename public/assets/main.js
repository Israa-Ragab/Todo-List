 /**
     * UI MODULE
     */
    const UI = (() => {
      const DOM = {
        input: document.getElementById("input"),
        addBtn: document.getElementById("add-btn"),
        priority: document.getElementById("priority"),
        dropdown: document.getElementById("dropdown"),
        list: document.getElementById("task-list"),
        clearAll: document.getElementById("clear-all")
      };

      const _resetPrior = () => {
        DOM.priority.className = "btn btn-secondary dropdown-toggle";
        DOM.addBtn.className = "btn btn-primary";
        DOM.input.className = "form-control";
      };

      const _addPrior = (color) => {
        DOM.priority.classList.add(`btn-${color}`);
        DOM.addBtn.classList.add(`btn-${color}`);
        DOM.input.classList.add(`border-${color}`);
      };

      const updatePrior = (priority) => {
        if (priority === "1") {
          _resetPrior();
          _addPrior("danger");
        } else if (priority === "2") {
          _resetPrior();
          _addPrior("warning");
        } else {
          _resetPrior();
        }
      };

      const clearInputs = () => {
        DOM.input.value = "";
        _resetPrior();
      };

      const renderTask = (text, priority, date, id) => {
        let span = "";
        if (priority === "1") {
          span = `<span class="badge bg-danger text-light ms-2">🔥 Very Hot</span>`;
        } else if (priority === "2") {
          span = `<span class="badge bg-warning text-dark ms-2">🔥 Hot</span>`;
        }

        let record = "";
        const timeRange = +new Date() - +date;
        if (timeRange < 86400000) {
          record = `${date.getHours().toString().padStart(2, "0")}:${date.getMinutes().toString().padStart(2, "0")}`;
        } else {
          record = `${Math.floor(timeRange / 86400000)} days ago`;
        }

        const html = `
        <div class="list-group-item" data-id="${id}">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <input class="form-check-input me-2 complete-btn" type="checkbox" id="check-${id}">
              <label class="form-check-label" for="check-${id}">${text} ${span}</label>
              <br><small class="text-muted">${record}</small>
            </div>
            <div class="task-actions d-flex gap-2">
              <button class="btn btn-sm btn-edit edit-btn"><i class="bi bi-pencil-square"></i></button>
              <button class="btn btn-sm btn-delete delete-btn"><i class="bi bi-trash"></i></button>
            </div>
          </div>
        </div>
        `;
        DOM.list.insertAdjacentHTML("afterbegin", html);
      };

      const removeTask = (el) => {
        el.remove();
      };

      const clearAllTasks = () => {
        DOM.list.innerHTML = "";
      };

      return {
        updatePrior,
        clearInputs,
        renderTask,
        removeTask,
        clearAllTasks,
        DOM
      };
    })();

    /**
     * TASKS
     */
    const tasks = (() => {
      const list = [];

      class Task {
        constructor(content, prior, date, id) {
          this.content = content;
          this.prior = prior;
          this.date = date;
          this.id = id;
        }
      }

      const addTask = (text, priority, date, id) => {
        list.push(new Task(text, priority, date, id));
      };

      const deleteTask = (id) => {
        const index = list.findIndex((task) => task.id === id);
        if (index > -1) list.splice(index, 1);
      };

      const clearAll = () => {
        list.length = 0;
      };

      return {
        addTask,
        deleteTask,
        clearAll,
        list
      };
    })();

    /**
     * MAIN CONTROLLER
     */
    const main = (() => {
      const state = { prior: 0 };

      const newTask = () => {
        state.currentDate = new Date();
        state.currentID = (+state.currentDate).toString(16);
        tasks.addTask(UI.DOM.input.value, state.prior, state.currentDate, state.currentID);
        UI.renderTask(UI.DOM.input.value, state.prior, state.currentDate, state.currentID);
        UI.clearInputs();
        state.prior = 0;
      };

      // Priority dropdown
      UI.DOM.dropdown.addEventListener("click", (e) => {
        const prior = e.target.dataset.prior;
        if (prior !== undefined) {
          state.prior = prior;
          UI.updatePrior(prior);
        }
      });

      // Add task
      UI.DOM.addBtn.addEventListener("click", () => {
        if (UI.DOM.input.value.trim() !== "") newTask();
      });

      UI.DOM.input.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && UI.DOM.input.value.trim() !== "") {
          e.preventDefault();
          newTask();
        }
      });

      // Clear all
      UI.DOM.clearAll.addEventListener("click", () => {
        tasks.clearAll();
        UI.clearAllTasks();
      });

      // Task actions
      UI.DOM.list.addEventListener("click", (e) => {
        const currentTask = e.target.closest(".list-group-item");

        // ✅ اكتمال التاسك
        if (e.target.classList.contains("complete-btn")) {
          currentTask.classList.toggle("bg-success");
          currentTask.classList.toggle("text-white");
          currentTask.querySelector("label").classList.toggle("text-decoration-line-through");
          return;
        }

        // 🗑 مسح التاسك
        if (e.target.closest(".delete-btn")) {
          tasks.deleteTask(currentTask.dataset.id);
          UI.removeTask(currentTask);
          return;
        }

        // ✏️ تعديل التاسك
        if (e.target.closest(".edit-btn")) {
          const label = currentTask.querySelector("label");
          const oldText = label.childNodes[0].textContent.trim();

          if (currentTask.querySelector(".edit-form")) return;

          const editForm = document.createElement("div");
          editForm.className = "edit-form mt-3 p-3 border rounded bg-light";
          editForm.innerHTML = `
            <input type="text" class="form-control mb-2" value="${oldText}">
            <div class="d-flex gap-2">
              <button class="btn btn-success btn-sm save-edit"><i class="bi bi-check-circle me-1"></i> Save</button>
              <button class="btn btn-secondary btn-sm cancel-edit"><i class="bi bi-x-circle me-1"></i> Cancel</button>
            </div>
          `;

          currentTask.appendChild(editForm);

          // 🎯 Save
          editForm.querySelector(".save-edit").addEventListener("click", () => {
            const newText = editForm.querySelector("input").value.trim();
            if (newText !== "") {
              label.childNodes[0].textContent = newText + " ";
              const task = tasks.list.find((t) => t.id === currentTask.dataset.id);
              if (task) task.content = newText;
            }
            editForm.remove();
          });

          // ❌ Cancel
          editForm.querySelector(".cancel-edit").addEventListener("click", () => {
            editForm.remove();
          });

          editForm.querySelector("input").focus();
          return;
        }
      });
    })();