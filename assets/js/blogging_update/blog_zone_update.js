function compile_blog_node(blog, at_end=false) 
{
    var data = `<section aria-label="${blog.title}"><p><a class="link" href="${blog.link}" aria-label="Date de publication: ${blog.date}">${blog.date}</a></p><h1><a class="title_link" href="${blog.link}" aria-label="Titre: ${blog.title}">
        ${blog.title}
</a></h1>
    <p aria-label="Content: ${blog.brief}">
        ${blog.brief}
    </p>
    <a class="link" href="${blog.link}" aria-label="Read more: Lire la suite ${blog.title}">Lire la suite ${blog.title}</a>
</section>`;
    if (!at_end) {
        data+="<br><hr><br>";
    }
    return data;
}

function latest_blogs_update(ID, blogs) {
    var element = document.getElementById(ID);
    var data = "<h1>Articles récents</h1><ul>";
    if (!element) {
        return;
    }
    for (var article = 0; article < blogs.length && article < 4; article++) {
        var blog = blogs[article];
        data += `<li><a class="link" href="${blog.url}">${blog.title}</a></li>`
    }
    data += "</ul>";
    element.innerHTML = data;
}

function insert_articles_into_body(ID, blogs)
{
    var element = document.getElementById(ID);
    var data = "";
    var max_length = blogs.length;
    if (!element) {
        return;
    }
    for (var i = 0; i < max_length; i++) {
        var blog = blogs[i];
        if (i < max_length) {
            data += compile_blog_node(blog, false);
        } else {
            data += compile_blog_node(blog, true);
        }   
    }
    element.innerHTML = data;
}

function count_nb_blogs(jsonData, theme) {
    var nb_blogs = 0;
    
    for (var i = 0; i < jsonData.length; i++) {
        var data = jsonData[i];
        if (data.link.includes(theme)) {
            nb_blogs++;
        }
    }
    return nb_blogs;
}

function filter_blogs_by_theme(ID, ID2, jsonData) {
    var theme = window.location.href;
    if (theme.endsWith("/")) {
        theme = theme.slice(0, -1);
    }
    theme = theme.split("/");
    theme = theme[theme.length - 1];
    console.log(`theme found = ${theme}`);
    var blogs = "";
    var nb_added_blogs = 0;
    var nb_blogs = count_nb_blogs(jsonData, theme);
    var recent_blogs = "<h1>Articles récents</h1><ul>";
    
    for (var i = 0; i < nb_blogs; i++) {
        var blog = jsonData[i];
        if (blog.link.includes(theme)) {
            if (nb_added_blogs < 4) {
                recent_blogs+=`<li><a class="link" href="${blog.url}">${blog.title}</a></li>`;
            }
            if (nb_added_blogs < nb_blogs - 1) {
                blogs += compile_blog_node(blog, false);
            } else {
                blogs += compile_blog_node(blog, true);
            }
            nb_added_blogs++;
        }
    }
    recent_blogs += "</ul>";
    var elem1 = document.getElementById(ID);
    var elem2 = document.getElementById(ID2);
    console.log(`blogs = ${blogs}, recent_blogs = ${recent_blogs}`);
    if (elem1) {
        console.log(`ID1 = ${ID}, found`);
        elem1.innerHTML = blogs;
    }
    if (elem2) {
        console.log(`ID2 = ${ID2}, found`);
        elem2.innerHTML = recent_blogs;
    }
}

async function fetch_JSON_file(filePath) {
    try {
        const response = await fetch(filePath);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return await response.json(); // Return the parsed JSON data
    } catch (error) {
        console.error('There was a problem fetching the JSON file:', error);
    }
}

fetch_JSON_file('/assets/js/blogging_update/blog_list.json')
    .then(blogs => {
        latest_blogs_update("recent_articles", blogs);
        insert_articles_into_body("blog_section", blogs);
        filter_blogs_by_theme("blog_theme", "recent_theme_articles", blogs);
    })
    .catch(error => {
        console.error('Erreur lors de la récupération du fichier JSON:', error);
    });