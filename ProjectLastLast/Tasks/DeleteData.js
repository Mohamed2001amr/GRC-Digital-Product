async function confirmDelete(taskId="Nothing") {
    const taskIdd = document.getElementById("deleteTask").getAttribute("data-taskid")??null;
    
    if (taskId=="Nothing") {
        taskId = taskIdd;
    }

    const result = await Swal.fire({
        title: 'Are you sure you want to delete?',
        text: "This will delete the task and all related data!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'No, cancel',
    });

    if (!result.isConfirmed) {
        Swal.fire('Canceled', 'The task is still intact.', 'error');
        return;
    }

    // Show loading message during deletion
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the task and related data.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        // Step 1: Delete actions
        const actionsResponse = await fetch(`${APIGateWay}TaskActions.php?taskId=${taskId}`, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        });

        const actions = await actionsResponse.json();
        if (actions && actions.length > 0) {
            await deleteActions(taskId); // wait for the deleteActions function to finish
        }

        // Step 2: Delete assignments
        const assignmentsResponse = await fetch(`${APIGateWay}TaskAssignments.php?taskId=${taskId}`, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        });

        const assignments = await assignmentsResponse.json();
        if (assignments && assignments.length > 0) {
            await deleteAssignments(taskId); // wait for the deleteAssignments function to finish
        }

        // Step 3: Delete attachments
        const attachmentsResponse = await fetch(`${APIGateWay}TaskAttachment.php?taskId=${taskId}`, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        });

        const attachments = await attachmentsResponse.json();
        if (attachments && attachments.length > 0) {
            await deleteAttachments(taskId); // wait for the deleteAttachments function to finish
        }

        // Step 4: Delete the task itself
        await deleteTask(taskId); // wait for the deleteTask function to finish

        // When everything is done
        Swal.close();
        Swal.fire(
            'Deleted!',
            'The task and its related data have been successfully deleted.',
            'success'
        ).then(() => {
            location.reload(); // Reload the page or perform other actions
        });
    } catch (error) {
        console.error('Error during deletion:', error);
        Swal.close(); // Close loading modal
        Swal.fire('Error!', error.message || 'An unexpected error occurred.', 'error');
    }
}




// Function to delete attachments
function deleteAttachments(TaskId) {
    return fetch(`${APIGateWay}TaskAttachment.php`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ TaskId }),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Failed to delete attachments. Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.error || 'Failed to delete attachments.');
            }
        });
}

// Function to delete actions
function deleteActions(TaskId) {
    return fetch(`${APIGateWay}TaskActions.php`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ TaskId }),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Failed to delete actions. Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.error || 'Failed to delete actions.');
            }
        });
}

// Function to delete assignments
function deleteAssignments(TaskId) {
    return fetch(`${APIGateWay}TaskAssignments.php`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ TaskId }),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Failed to delete assignments. Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.error || 'Failed to delete assignments.');
            }
        });
}


function deleteTask(TaskId) {
    if (!TaskId) {
        console.error('Task ID is required to delete.');
        return Promise.reject(new Error('Task ID is missing.'));
    }

    // Make a DELETE request to the API
    return fetch(`${APIGateWay}Task.php`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ TaskId }), // Send the TaskId in the body
    })
    .then(response => {
        // Ensure the response is successful
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Validate the success field in the response
        if (data.success) {
            return true; // Deletion was successful
        } else {
            throw new Error(data.error || 'Failed to delete task.');
        }
    });
}
