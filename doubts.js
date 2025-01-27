document.getElementById('doubt-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const subject = document.getElementById('subject').value;
    const question = document.getElementById('question').value;
    const attachment = document.getElementById('attachment').files[0];

    // Form submission handling (no actual backend in this case)
    alert('Doubt submitted successfully!');

    // After submission, reset the form
    document.getElementById('doubt-form').reset();

    // Optionally, you can update the recent doubts list
    addDoubtToList(subject, question);
});

// Function to dynamically add a new doubt to the recent doubts list
function addDoubtToList(subject, question) {
    const doubtsList = document.getElementById('doubts-list');
    const newDoubt = document.createElement('li');
    newDoubt.innerHTML = `<strong>${subject}</strong>: ${question}`;
    doubtsList.appendChild(newDoubt);
}
