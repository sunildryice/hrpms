# Work Plan Rules

This document outlines the business logic, permissions, and user interactions related to the **Work Plan** module in HRPMS.

## 1. Creating and Editing Work Plans

The ability for a user to create or edit a work plan is time-sensitive and strictly controlled to ensure data integrity.

### Rules:

- **Future Weeks**: Users can freely create and edit work plans for any upcoming week.
- **Current Week**: Users can create or edit the work plan for the current week, but **only up to Monday**. From Tuesday onwards, the plan for the current week is locked.
- **Past Weeks**: Work plans for past weeks cannot be created or edited.

This logic is enforced by the `WorkPlanPolicy` (`update` method).

## 2. Updating Work Plan Status

In the Work Plan details view, users can update the status of individual tasks. The permission to do so depends on the week in question.

### Rules:

- **Previous Week**: Users are allowed to update the status of tasks from the immediately preceding week. This allows them to finalize their work logs after the week has concluded.
- **Current Week**: Status updates for the current week are restricted and can **only be performed on Friday**.
- **Other Weeks**: Status updates are disabled for all other weeks (e.g., two weeks prior, or any future week).

This logic is enforced by the `WorkPlanPolicy` (`updateStatus` method).

### Status Change Workflow

The user interface adapts based on the task's status:

1.  **Editable Status**: For tasks where the status can be changed, a dropdown (select input) is displayed with options like `Not Started`, `In Progress`, `Completed`, etc.

2.  **Finalized Status**: If a task's status is set to **`Completed`** or **`No Required`**, the dropdown is replaced by a static status badge. These statuses are considered final and cannot be changed back.

3.  **Remarks for Final Statuses**:
    - When a user changes a status to **`Completed`**, a modal dialog appears, requiring them to enter **Remarks** before saving.
    - Similarly, changing a status to **`No Required`** also prompts a modal for **Remarks**.

This behavior is managed within the `WorkPlanDetailController` and the associated frontend scripts. The controller ensures that a reason (remark) is provided when a final status is set.
