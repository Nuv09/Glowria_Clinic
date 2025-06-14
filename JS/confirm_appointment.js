
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".confirm-btn").forEach(button => {
      button.addEventListener("click", function () {
        const appointmentId = this.dataset.id;
        const row = this.closest("tr");
  
        fetch("Confirm_Appointment.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: "id=" + encodeURIComponent(appointmentId)
        })
        .then(response => response.text())
        .then(data => {
          if (data.trim() === "true") {
           
            row.querySelector(".status-text").textContent = "Confirmed";
            
            this.remove();
            
            const prescribeDiv = document.createElement("div");
            prescribeDiv.style.marginTop = "5px";
            prescribeDiv.innerHTML = `<a class="Prescribe" href="Prescribe_Medication_Page.php?appointment_id=${appointmentId}">Prescribe</a>`;
            row.querySelector(".status-cell").appendChild(prescribeDiv);
          } else {
            alert("Failed to confirm appointment.");
          }
        })
        .catch(() => {
          alert("AJAX error.");
        });
      });
    });
  });