// script.js

function openTab(evt, tabName) {
    var i, tabcontent, tablinks;

    // إخفاء جميع محتويات التبويبات
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // إزالة حالة "النشط" من جميع الأزرار
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace("active","");
    }

    // عرض التبويب المطلوب وإضافة حالة "النشط" للزر الخاص به
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

// فتح التبويب الافتراضي عند تحميل الصفحة
document.getElementById("defaultOpen").click();



//change Container Background when click on the checkbox
function changeContainerBackground(checkbox) {
    const container =  checkbox.closest('.table');
    
    // التحقق مما إذا كان أي checkbox مفعّل
    const checkboxes = container.querySelectorAll('input[type="checkbox"]');
    let isChecked = false;

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            isChecked = true;
        }
    });

    // إذا تم تفعيل أي checkbox، قم بتغيير الخلفية
    if (isChecked) {
        container.classList.add('active');
    } else {
        container.classList.remove('active');
    }
}


//Remove section
function myFunction() {
    const list = document.getElementById('section');
    list.parentNode.removeChild(list);
}
