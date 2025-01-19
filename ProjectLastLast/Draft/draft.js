import React, { useState } from "react";

const TaskApp = () => {
  // قائمة المهام (مثال)
  const tasks = [
    { id: 1, name: "Task 1", details: "Details of Task 1" },
    { id: 2, name: "Task 2", details: "Details of Task 2" },
    { id: 3, name: "Task 3", details: "Details of Task 3" },
    { id: 4, name: "Task 4", details: "Details of Task 4" },
  ];

  // حالة المهمة الحالية
  const [currentTaskIndex, setCurrentTaskIndex] = useState(0);

  // الدالة للتنقل إلى المهمة التالية
  const handleNext = () => {
    if (currentTaskIndex < tasks.length - 1) {
      setCurrentTaskIndex(currentTaskIndex + 1);
    }
  };

  // الدالة للتنقل إلى المهمة السابقة
  const handlePrevious = () => {
    if (currentTaskIndex > 0) {
      setCurrentTaskIndex(currentTaskIndex - 1);
    }
  };

  // المهمة الحالية
  const currentTask = tasks[currentTaskIndex];

  return (
    <div style={{ padding: "20px", fontFamily: "Arial, sans-serif" }}>
      <h1>Task Viewer</h1>
      
      {/* جدول المهام */}
      <table border="1" cellPadding="10" style={{ marginBottom: "20px", width: "100%" }}>
        <thead>
          <tr>
            <th>ID</th>
            <th>Task Name</th>
          </tr>
        </thead>
        <tbody>
          {tasks.map((task, index) => (
            <tr
              key={task.id}
              style={{
                backgroundColor: index === currentTaskIndex ? "#f0f8ff" : "white",
              }}
            >
              <td>{task.id}</td>
              <td>{task.name}</td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* عرض المهمة الحالية */}
      <div style={{ marginBottom: "20px" }}>
        <h2>Current Task</h2>
        <p>
          <strong>ID:</strong> {currentTask.id}
        </p>
        <p>
          <strong>Name:</strong> {currentTask.name}
        </p>
        <p>
          <strong>Details:</strong> {currentTask.details}
        </p>
      </div>

      {/* أزرار التنقل */}
      <div>
        <button onClick={handlePrevious} disabled={currentTaskIndex === 0}>
          ⬅️ Previous
        </button>
        <button onClick={handleNext} disabled={currentTaskIndex === tasks.length - 1}>
          Next ➡️
        </button>
      </div>
    </div>
  );
};

export default TaskApp;
