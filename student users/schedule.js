// Wait for the DOM to fully load before running JS
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('scheduleForm');
  const tableBody = document.querySelector('#scheduleTable tbody');

  // Handle form submission
  form.addEventListener('submit', (event) => {
    event.preventDefault(); // Prevent page reload

    // Get form values
    const course = document.getElementById('course').value;
    const day = document.getElementById('day').value;
    const time = document.getElementById('time').value;

    // Create a new table row with the submitted data
    const newRow = document.createElement('tr');

    const courseCell = document.createElement('td');
    courseCell.textContent = course;

    const dayCell = document.createElement('td');
    dayCell.textContent = day;

    const timeCell = document.createElement('td');
    timeCell.textContent = time;

    // Append cells to row
    newRow.appendChild(courseCell);
    newRow.appendChild(dayCell);
    newRow.appendChild(timeCell);

    // Append row to table
    tableBody.appendChild(newRow);

    // Reset form
    form.reset();
  });
});
