function calculateTimeDifference() {
    const inputDate = document.getElementById("date-input").value;

    const inputDateTime = new Date(inputDate);

    const today = new Date();
    const timeDiff = Math.abs(today - inputDateTime);
    const years = Math.floor(timeDiff / (1000 * 60 * 60 * 24 * 365.25));
    const months = Math.floor((timeDiff % (1000 * 60 * 60 * 24 * 365.25)) / (1000 * 60 * 60 * 24 * 30.4375));
    const days = Math.floor((timeDiff % (1000 * 60 * 60 * 24 * 30.4375)) / (1000 * 60 * 60 * 24));

    const outputElement = document.getElementById("output");
    outputElement.innerHTML = `Az állat ${years} éves, ${months} hónapos, és ${days} napos.`;
}