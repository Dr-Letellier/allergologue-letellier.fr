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
    if (!element) {
        return;
    }
    for (var i = 0; i < blogs.length; i++) {
        var blog = blogs[i];
        data += `<section aria-label="${blog.title}"><p><a class="link" href="${blog.link}" aria-label="Date de publication: ${blog.date}">${blog.date}</a></p><h1><a class="title_link" href="${blog.link}" aria-label="Titre: ${blog.title}">
        ${blog.title}
</a></h1>
    <p aria-label="Content: ${blog.brief}">
        ${blog.brief}
    </p>
    <a class="link" href="${blog.link}" aria-label="Read more: Lire la suite ${blog.title}">Lire la suite ${blog.title}</a>
</section>`;
    }
    element.innerHTML = data;
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

fetch_JSON_file('assets/js/blogging_update/blog_list.json')
    .then(blogs => {
        latest_blogs_update("recent_articles", blogs);
        insert_articles_into_body("blog_section", blogs);
    })
    .catch(error => {
        console.error('Erreur lors de la récupération du fichier JSON:', error);
    });