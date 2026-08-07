const questionElement = document.getElementById('question');
const optionsElement = document.getElementById('options');

const quizData = [
    {
        question: 'What is the SDLC acronym commonly associated with?',
        options: ['Software Development Licensing Committee', 'System Design and Logical Computing', 'Software Development Life Cycle', 'Security Development Lifecycle'],
        correctAnswer: 'Software Development Life Cycle'
    },
    {
        question: 'What does "Agile" refer to in software development?',
        options: ['A programming language', 'A type of software architecture', 'A methodology for iterative and incremental development', 'A software testing technique'],
        correctAnswer: 'A methodology for iterative and incremental development'
    },
    {
        question: 'What is the purpose of version control systems like Git?',
        options: ['To optimize code execution speed', 'To create visually appealing user interfaces', 'To manage and track changes to source code', 'To prevent software piracy'],
        correctAnswer: 'To manage and track changes to source code'
    },
    {
        question: 'Which software development model involves breaking down a project into small, manageable phases?',
        options: ['Waterfall model', 'Spiral model', 'Iterative model', 'Incremental model'],
        correctAnswer: 'Iterative model'
    },
    {
        question: 'What is the role of a software architect?',
        options: ['Writing code for the entire project', 'Managing project timelines and budgets', 'Designing the overall structure of a software system', 'Creating user documentation'],
        correctAnswer: 'Designing the overall structure of a software system'
    }
];

let currentQuestionIndex = 0;
let score = 0;
let startTime;

function showQuestion(questionObj) {
    startTime = new Date();
    questionElement.textContent = questionObj.question;
    optionsElement.innerHTML = '';

    questionObj.options.forEach((option, index) => {
        const optionButton = document.createElement('button');
        optionButton.textContent = option;
        optionButton.classList.add('option-btn');
        optionButton.addEventListener('click', () => checkAnswer(option, questionObj.correctAnswer));
        optionsElement.appendChild(optionButton);
    });
}

function checkAnswer(selectedOption, correctAnswer) {
    if (selectedOption === correctAnswer) {
        score++;
    }

    currentQuestionIndex++;
    if (currentQuestionIndex < quizData.length) {
        showQuestion(quizData[currentQuestionIndex]);
    } else {
        showResult();
    }
}

function showResult() {
    const endTime = new Date();
    const duration = Math.round((endTime - startTime) / 1000);

    questionElement.textContent = '';
    optionsElement.innerHTML = '';

    const resultElement = document.createElement('div');
    resultElement.classList.add('result');
    resultElement.textContent = `Quiz completed!\nYour score: ${score} out of ${quizData.length}\nTime taken: ${duration} seconds`;

    optionsElement.appendChild(resultElement);
}

// Initialize the quiz with the first question
showQuestion(quizData[currentQuestionIndex]);
