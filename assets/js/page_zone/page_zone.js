function insert_page_path_zone(ID) {
    var elem = document.getElementById(ID);
    var currentURL = window.location.href;
    var result = "<p class='paragraph_page_zone'>";
    var url_buffer = "";
    if (!elem) {
        return;
    }
    if (currentURL.startsWith("http")) {
        url_buffer = "http://";
    } else {
        url_buffer = "https://";
    }
    currentURL = currentURL.split("?")[0];
    currentURL = currentURL.replace("https://", "").replace("http://", "");
    currentURL = currentURL.split("/");
    if (currentURL[currentURL.length - 1 ] == '') {
        currentURL.pop(currentURL.length);
    }
    var max_length = currentURL.length;
    console.log(`Current URL = ${currentURL}`);
    for (var i = 0; i < max_length; i++) {
        url_buffer += currentURL[i] + "/";
        result+=`<a href='${url_buffer}' class="url_node_deco_link">${currentURL[i]}</a>`;
        if (i < max_length - 1) {
            result+="&gt;";
        }
    }
    result+="</p>";
    elem.innerHTML = result;
}

insert_page_path_zone("path_to_page");