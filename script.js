
function clamp(input, min, max) {
    if (input < min)
        return min;
    if (input > max)
        return max;
    return input;
}

function clampMin(input, min) {
    if (input < min)
        return min;
    return input;
}
function clampMax(input, max) {
    if (input > max)
        return max;
    return input;
}

function PageHeight() {
    var body = document.body,
        html = document.documentElement;

    var height = Math.max(body.scrollHeight, body.offsetHeight,
        html.clientHeight, html.scrollHeight, html.offsetHeight);
    return height;
}

function highlightFor(id, color, seconds) {
    var element = document.getElementById(id)
    var origcolor = element.style.backgroundColor
    element.style.backgroundColor = color;
    var t = setTimeout(function () {
        element.style.backgroundColor = origcolor;
    }, (seconds * 1000));
}

function scroll_To(id) {
    try {
        document.getElementById(`${id}`).scrollIntoView({
            behavior: "smooth",
            block: "start",
            inline: "nearest"
        });
        highlightFor(id, "#D6BAD0", 1);
    }
    catch { }
}

function DeleteConfirmation(GET_url, CustomText = "Are you sure you want to delete this record?") {
    var del = confirm(CustomText);
    if (del == true) {
        window.location.href = GET_url;
    }
    return del;
}

// radioBtn is the id of a radio button that needs to be flicked
// on after this function is called
function enableButtons(radioBtn, buttonClass) {
    document.getElementById(radioBtn).checked = true;

    var elements = document.getElementsByClassName(buttonClass);

    for (var i = 0; i < elements.length; i++) {
        elements[i].disabled = false;
    }
}




// class CLS_Drag {

//     constructor(itemToDrag, itemToGrip) {
//         this.DragItem = document.getElementById(`${itemToDrag}`);
//         this.Container = document.getElementById(`${itemToGrip}`);

//         //var elemLocation = dragItem.getBoundingClientRect();
//         //alert(elemLocation.left);
//         // var left = elemLocation.left;
//         // var pageHeight = PageHeight();

//         this.active = false;
//         this.currentX;
//         this.currentY;
//         this.initialX;
//         this.initialY;
//         this.xOffset = 0;
//         this.yOffset = 0;

//         this.Container.addEventListener("mousedown", this.dragStart, false);
//         window.addEventListener("mouseup", this.dragEnd, false);
//         window.addEventListener("mousemove", this.drag, false);

//         //alert(elemLocation.left);
//     }

//     dragStart(e) {
//         this.initialX = e.clientX - this.xOffset;
//         this.initialY = e.clientY - this.yOffset;

//         console.log(this.active);

//         this.active = true;

//         var elemLocation = this.DragItem.getBoundingClientRect();
//         console.log(elemLocation.left);
//     }

//     dragEnd(e) {
//         this.initialX = this.currentX;
//         this.initialY = this.currentY;

//         this.active = false;
//         console.log("click end");
//     }

//     drag(e) {
//         //if (this.active) {
//         e.preventDefault();

//         this.currentX = e.clientX - this.initialX;
//         this.currentY = e.clientY - this.initialY;

//         this.xOffset = this.currentX;
//         this.yOffset = this.currentY;

//         //this.setTranslate();
//         //this.DragItem.style.transform = "translate3d(" + this.currentX + "px, " + this.currentY + "px, 0)";
//         //alert("click move");
//         //}
//         //console.log("this.active");;
//     }

//     setTranslate() {
//         this.DragItem.style.transform = "translate3d(" + this.currentX + "px, " + this.currentY + "px, 0)";
//     }
// }