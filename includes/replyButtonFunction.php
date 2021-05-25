    <script>
        var toggle = 0;

        function changeLabel(articleId, commentId = -1) {
            var item = document.getElementById("replyBox");
            var hidden = document.getElementById("hiddenElmArticleId");
            var drag = document.getElementById("replyBoxDragBar");
            var textArea = document.getElementById("commentBodyTextId");
            item.style.display = 'block';

            if (commentId == -1)
                tinymce.activeEditor.execCommand('mceInsertContent', true, "<@T" + articleId + ">");
            //textArea.textContent += `<@T${articleId}>`; // T for thread cause i need some way to tell comment from thread
            else
                tinymce.activeEditor.execCommand('mceInsertContent', true, "<@C" + commentId + ">");
            //textArea.textContent += `<@C${commentId}>`; // C for comment

            drag.textContent = `Reply To Thread ${articleId}`;
            hidden.value = `${articleId}`;
        }
    </script>