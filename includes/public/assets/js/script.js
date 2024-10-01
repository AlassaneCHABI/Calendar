
const daysTag = document.querySelector(".days"),
    currentDate = document.querySelector(".current-date"),
    prevNextIcon = document.querySelectorAll(".icons span");

let date = new Date(),
    currYear = date.getFullYear(),
    currMonth = date.getMonth();

const months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet",
                "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
        // fonction de rendu du calendrier
                const renderCalendar = () => {
                    let firstDayofMonth = new Date(currYear, currMonth, 1).getDay(),
                        lastDateofMonth = new Date(currYear, currMonth + 1, 0).getDate(),
                        lastDayofMonth = new Date(currYear, currMonth, lastDateofMonth).getDay(),
                        lastDateofLastMonth = new Date(currYear, currMonth, 0).getDate();
                    let liTag = "";
                
                    // Display the previous month's last days as inactive
                    for (let i = firstDayofMonth; i > 0; i--) {
                        let dateStr = `${currYear}-${String(currMonth).padStart(2, '0')}-${lastDateofLastMonth - i + 1}`;
                        liTag += `<li class="inactive" data-prev-month-date="${dateStr}">${lastDateofLastMonth - i + 1}</li>`;
                    }
                
                    // Display current month's days
                    for (let i = 1; i <= lastDateofMonth; i++) {
                        let dateStr = `${currYear}-${String(currMonth + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                        let isToday = i === date && currMonth === new Date().getMonth() && currYear === new Date().getFullYear() ? "active" : "";                       
                        
                        let dateExists = events.some(event => event.date === dateStr);

                        $dashed = dateExists ? "dashed" : "" ; 

                        liTag += `<li class="${isToday} ${$dashed}" data-date="${dateStr}">${i} <span></span></li>`;
                    }
                
                    // Display next month's first days as inactive
                    for (let i = lastDayofMonth; i < 6; i++) {
                       // let dateStr = `${currYear}-${String(currMonth + 2).padStart(2, '0')}-${i - lastDayofMonth + 1}`;
                        let dateStr = `${currYear}-${String(currMonth + 2).padStart(2, '0')}-${String(i - lastDayofMonth + 1).padStart(2, '0')}`;

                        liTag += `<li class="inactive" data-next-month-date="${dateStr}">${i - lastDayofMonth + 1}</li>`;
                    }
                
                    currentDate.innerText = `${months[currMonth]} ${currYear}`;
                    daysTag.innerHTML = liTag;
                
                    // Add click event listeners for each day
                    const dayElements = document.querySelectorAll('.days li[data-date], .days li[data-prev-month-date], .days li[data-next-month-date]');
                    dayElements.forEach(dayElement => {
                        dayElement.addEventListener('click', () => {
                            let selectedDate = dayElement.getAttribute('data-date');
                            
                            if (dayElement.hasAttribute('data-prev-month-date')) {
                                currMonth--;
                                if (currMonth < 0) {
                                    currMonth = 11;
                                    currYear--;
                                }

                                selectedDate = dayElement.getAttribute('data-prev-month-date');
                                renderCalendar();
                                scrollToMonthAndHighlight(selectedDate);
                                highlightEvent(selectedDate);
                            } else if (dayElement.hasAttribute('data-next-month-date')) {
                                currMonth++;
                                if (currMonth > 11) {
                                    currMonth = 0;
                                    currYear++;
                                }

                                selectedDate = dayElement.getAttribute('data-next-month-date');
                                renderCalendar();
                                scrollToMonthAndHighlight(selectedDate);
                                highlightEvent(selectedDate);
                            } else {
                                highlightCalendarCell(selectedDate);
                                highlightEvent(selectedDate);
                            }
                        });
                    });
                    
                populateEventList();
                // mettre en avant la date d'aujourd'hui
                const  dateT = new Date() 
                highlightCalendarCell(dateT.getFullYear()+"-"+(dateT.getMonth()+1).toString().padStart(2, '0')+"-"+dateT.getDate().toString().padStart(2, '0'))
                highlightEvent(dateT.getFullYear()+"-"+(dateT.getMonth()+1).toString().padStart(2, '0')+"-"+dateT.getDate().toString().padStart(2, '0'))
                    // Update event list for the current month
                };

renderCalendar();

prevNextIcon.forEach(icon => {
    icon.addEventListener("click", () => {
        currMonth = icon.id === "prev" ? currMonth - 1 : currMonth + 1;

        if (currMonth < 0 || currMonth > 11) {
            date = new Date(currYear, currMonth, new Date().getDate());
            currYear = date.getFullYear();
            currMonth = date.getMonth();
        } else {
            date = new Date();
        }

        renderCalendar();  // Re-render calendar after month change
    });
});

// Function to display the list of events
function populateEventList() {
    const eventListElement = document.getElementById('event-list');
    const month = currMonth;
    const year = currYear;

    let daysInMonth = new Date(year, month + 1, 0).getDate();
    let html = '';

    // Display events for each day of the current month
    for (let day = 1; day <= daysInMonth; day++) {
        let dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        let eventsForDateItems = events.find(ev => ev.date === dateStr) ;
        let eventsForDate = eventsForDateItems?.events || [];

        html += `  
            <div class="event-list-item" data-date="${dateStr}">
              <div class="event-date bg-light-green p-2 mb-2">${new Date(dateStr).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' })}</div>
       
            <div class="row">
                    <div class="col-2 event-icons">
                        <button class="btn btn-light me-2" style="margin: 20% 5px 0 0;" onclick="openModal_add_even('${dateStr}')">
                        <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
              `;

        if (eventsForDate.length > 0) {
            i = 0;
            eventsForDate.forEach(event => {
                ++i;
                html += `  
                  
                        <div  class="col-10 ${i > 1 ? 'offset-2':''}" onclick="${event.byMe ? `openModal_show_even_by_me(${event.id})` : `openModal_show_even(${event.id})`}">
                          <div style="background-color:${event.color}" class="${ event.byMe == true ? 'event-card' : 'event-card-invited'}   bg-pink p-2 d-flex justify-content-between align-items-center mb-4">
                            <div class="event-info">
                              <p class="mb-1 time-range">${event.startTime} - ${event.endTime}  </p>
                              <p class="mb-0 event-title">${event.title}</p>
                            </div>
                            <div class="event-icons d-flex align-items-center">
                              <button class="btn btn-light me-2">
                                <i class="bi ${ event.byMe == true ? 'bi-image' : 'bi-check'} "></i>
                              </button>
                              <button  data-share="${event.link}" class="btn btn-light">
                                <i class="bi ${ event.byMe == true ? 'bi-send' : 'bi-x'} "></i>
                              </button>
                            </div>
                          </div>
                        </div>
                    
                `;
            });
        } else {
            html += `  
                 
                    <div class="col-10">
                      <div class="event-card bg-pink p-2 d-flex justify-content-between align-items-center mb-4">
                        <div class="event-info">
                          <p class="mb-0 event-title">Aucun événement</p>
                        </div>
                      </div>
                    </div>
            
            `;
        }

        html += `</div> </div>`;
    }

    eventListElement.innerHTML = html;
}

// Highlight the selected calendar cell
function highlightCalendarCell(date) {
    const cells = document.querySelectorAll('.days li[data-date]');
    cells.forEach(cell => {
        if (cell.getAttribute('data-date') === date) {
            cell.classList.add('active');
        } else {
            cell.classList.remove('active');
        }
    });
}

// Highlight the selected event
function highlightEvent(date) {
    const items = document.querySelectorAll('#event-list .event-list-item');
    items.forEach(item => {
        if (item.getAttribute('data-date') === date) {
            item.classList.add('active');            
            item.scrollIntoView({ behavior: 'instant', block: 'start' });
            document.querySelector(".wrapper-side").scrollIntoView({ behavior: 'instant', block: 'start' });
        } else {
            item.classList.remove('active');
        }
    });
}

// Function to scroll and highlight the selected date after month switch
function scrollToMonthAndHighlight(date) {
    setTimeout(() => {
        highlightCalendarCell(date);
        highlightEvent(date);
        const element = document.querySelector(`li[data-date="${date}"]`);
        if (element) {
            element.scrollIntoView({ behavior: 'instant', block: 'start' });
            document.querySelector(".wrapper-side").scrollIntoView({ behavior: 'instant', block: 'start' });
        }
    }, 100); // Timeout to ensure the calendar has rendered the new month
}

populateEventList();

document.addEventListener('DOMContentLoaded', () => {
    const eventListElement = document.getElementById('event-list');
    const calendarElement = document.querySelector('.wrapper');

    const checkFixedPosition = () => {
        const items = document.querySelectorAll('#event-list .event-list-item');
        const calendarRect = calendarElement.getBoundingClientRect();
        
        items.forEach(item => {
            const itemRect = item.getBoundingClientRect();
            const eventDate = item.querySelector('.event-date');
           
            // 
            const element = document.querySelector(`li[data-date="${date}"]`);
            const Notelement = document.querySelectorAll(`li:not([data-date="${date}"])`);

            if (eventDate) { 
                if ((itemRect.top <= calendarRect.top+20 && window.innerWidth >470)  || itemRect.top <= calendarRect.bottom+20 && window.innerWidth<=470) { 
                    date = item.getAttribute('data-date');
                // console.log(window.innerWidth)
                    eventDate.classList.add('fixed');                    

                    // Vérifier si l'élément a été trouvé, puis effectuer une action
                    if (element) {
                        element.classList.add("active");
                    }

               //  highlightCalendarCell(date)
                } else {
                    //Notelement.classList.remove("active")
                    eventDate.classList.remove('fixed');                   
                        // Exemple : changer la couleur de fond de cet élément
                        Notelement.forEach(Nitem => {
                            Nitem.classList.remove("active");
                        })
                }
            }
        });
    };

    // Initial check
    checkFixedPosition();

    // Add scroll event listener to #event-list
    eventListElement.addEventListener('scroll', checkFixedPosition);
});

 
function selectBubble(element) {
    console.log("Couleur cliqeué déjà");
    // Supprimer la sélection précédente
    const bubbles = document.querySelectorAll('.color-bubble');
    bubbles.forEach(bubble => bubble.classList.remove('selected'));

    // Ajouter la classe "selected" à la bulle sélectionnée
    element.classList.add('selected');

    // Afficher la couleur sélectionnée
    const selectedColor = element.getAttribute('data-color');
    console.log(selectedColor);
    document.getElementById('selectedColor').value = selectedColor;
    const df = document.querySelectorAll('.selectedColor');
}