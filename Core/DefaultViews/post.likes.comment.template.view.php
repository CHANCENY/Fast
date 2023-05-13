<?php @session_start();

\UI\Comments::commentInit();
\UI\Comments::createTable();
$posts = \UI\Comments::loadAllPost();

if(\GlobalsFunctions\Globals::method() === "POST"){
    if(isset($_POST['post-thought-button'])){

        $user = \GlobalsFunctions\Globals::post('user');
        $title = \GlobalsFunctions\Globals::post('title');
        $mail = \GlobalsFunctions\Globals::post('email');
        $post = \GlobalsFunctions\Globals::post('textarea');
        $postId = \Json\Json::uuid();

        //saving comment
        $data = [
            'post_title'=>$title,
            'post_body'=>$post,
            'uid'=>$user,
            'post_uuid'=>$postId
        ];
        \UI\Comments::saveNewPost($data);
        $posts = \UI\Comments::loadAllPost();
    }

    if(isset($_POST['submit_replay_comment'])){

        $user = \GlobalsFunctions\Globals::post('user');
        $post = \GlobalsFunctions\Globals::post('post_uuid');
        $reply = \GlobalsFunctions\Globals::post('replay_textarea');

        $data = [
            'comment_body'=>$reply,
            'commentor_uid'=>$user,
            'post_id'=>$post,
        ];
        \UI\Comments::saveNewComment($data);
    }
}
$data = \UI\Pagination::pager($posts,'post-pagination', 5);
$posts = $data['data'];
$html = $data['html'];
$ids = [];
foreach ($posts as $key=>$po){
    $ids[] = $po['poid'];
}
$base = \GlobalsFunctions\Globals::protocal().'://'.\GlobalsFunctions\Globals::serverHost().'/'.\GlobalsFunctions\Globals::home();
?>

<?php  if(!empty($posts)): ?>
    <section data-bs-version="5.1" class="content7 cid-tCeIefzkiv" id="content7-i" data-uid="<?php echo \GlobalsFunctions\Globals::user()[0]['uid']; ?>" data-base="<?php echo $base; ?>" data-comment="<?php echo implode(',', $ids); ?>">
        <div class="container-fluid mb-lg-5">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 bg-light">
                    <?php foreach($posts as $key=>$post): ?>
                        <blockquote>
                            <h5 class="mbr-section-title mbr-fonts-style mb-2 display-7 flex">
                                <strong><?php echo $post['post_title']; ?></strong>
                                <img class="float-lg-end border border-white" style="width: 50px; height: 50px; border-radius: 100%; display: inline-flex;" src="<?php echo \User\User::loadUser($post['uid'])['profileImage']; ?>" alt="<?php echo \User\User::loadUser($post['uid'])['profileImage']; ?>">
                                <a class="poster-view?poster=<?php echo $post['uid']; ?>">view</a>
                            </h5>
                            <p class="mbr-text mbr-fonts-style display-4"><?php echo $post['post_body']; ?></p>
                            <div class="ms-auto">
                                <p><em>Posted <?php echo functions\to_time_ago($post['created']); ?></em><em>    By <?php
                                        echo \User\User::loadUser($post['uid'])['firstname'];
                                        ?></em></p>
                            </div>
                            <i id="like-post-id-<?php echo $post['poid']; ?>" data-owner="<?php echo \GlobalsFunctions\Globals::user()[0]['uid']; ?>" data-post="<?php echo $post['post_uuid']; ?>" class="mobi-mbri-like m-3"><?php echo \UI\Comments::getLikeCount($post['post_uuid']); ?></i>
                            <i id="comment-post-id-<?php echo $post['poid']; ?>"  data-owner="<?php echo \GlobalsFunctions\Globals::user()[0]['uid']; ?>" data-post="<?php echo $post['post_uuid']; ?>" class="mobi-mbri-chat"><?php echo \UI\Comments::getCommentsCount($post['post_uuid']); ?></i>
                        </blockquote>
                        <div id="comment-section-box-<?php echo $post['poid']; ?>"></div>
                    <?php endforeach; ?>
                    <div class="m-lg-3">
                        <?php echo $html; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php endif; ?>

<section data-bs-version="5.1" class="form5 cid-tCeIS3kWtk mb-5" id="form5-j">
    <div class="container-fluid">
        <div class="mbr-section-head">
            <h3 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2"><strong>Leave Your Thought</strong></h3>
            <h4 class="mbr-section-subtitle mbr-fonts-style align-center mb-0 mt-2 display-5">Thank you for deciding to share you experience<br> with the community</h4>
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-lg-8 mx-auto mbr-form" data-form-type="formoid">
                <form action="community" method="POST" class="mbr-form form-with-styler" data-form-title="Form Name">
                    <div class="row">
                        <input type="hidden" name="user" value="<?php echo \GlobalsFunctions\Globals::user()[0]['uid']; ?>">
                    </div>
                    <div class="dragArea row">
                        <div class="col-md col-sm-12 form-group mb-3" data-for="name">
                            <input type="text" name="title" placeholder="Title" data-form-field="name" class="form-control" value="" id="name-form5-j">
                        </div>
                        <div class="col-12 form-group mb-3" data-for="textarea">
                            <textarea name="textarea" placeholder="Thoughts" data-form-field="textarea" class="form-control" id="textarea-form5-j"></textarea>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 align-center mb-lg-5 mbr-section-btn"><button type="submit" name="post-thought-button" class="btn btn-primary display-4">Post your thought now!</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<div>
    <script type="application/javascript">
        const outerSectionTag = document.getElementById('content7-i');
        let baseUrl = outerSectionTag.getAttribute('data-base');
        const commentId = outerSectionTag.getAttribute('data-comment');

        const buildCommentSection = (data, idTag) =>{
            const id = idTag.id;
            let list = id.split('-');
            let last = list[list.length - 1];
            const commentSectionTag = document.getElementById('comment-section-box-'+last);
            console.log(data.length)
            if(data.length > 0){

                data.forEach((item)=>{
                    const divComment = document.createElement('div');
                    divComment.id="comments";
                    divComment.className = "bg-light border-start rounded p-3 mb-2";
                    const paragraph = document.createElement('p');
                    paragraph.className = "mbr-text mbr-fonts-style display-4";
                    paragraph.appendChild(document.createTextNode(item.comment_body));
                    divComment.appendChild(paragraph);
                    commentSectionTag.appendChild(divComment);
                })
                const formDiv = document.createElement('div');
                formDiv.className = "bg-light rounded  p-3 mb-3";
                const formTag = document.createElement('form');
                formTag.action = "community";
                formTag.method = "POST";
                let hiddenInput1 = document.createElement('input');
                hiddenInput1.type = 'hidden';
                hiddenInput1.id="replay-user-id";
                hiddenInput1.value = document.getElementById('content7-i').getAttribute('data-uid');
                hiddenInput1.name = "user";
                let hInput2 = document.createElement('input');
                hInput2.type = "hidden";
                hInput2.id = "post-id-id";
                hInput2.value = idTag.getAttribute('data-post');
                hInput2.name = "post_uuid";
                formTag.appendChild(hiddenInput1);
                formTag.appendChild(hInput2);

                const divTextArea = document.createElement('div');
                divTextArea.className = "col-12 form-group mb-3";
                const textArea = document.createElement('textarea');
                textArea.id = "reply-comment-id";
                textArea.name = "replay_textarea";
                textArea.className = "form-control";
                textArea.placeholder = "Write comment";
                divTextArea.appendChild(textArea);

                const buttonDiv = document.createElement('div');
                buttonDiv.className = "col-lg-12 col-md-12 col-sm-12 align-center mb-lg-5 mbr-section-btn";
                const buttonSub = document.createElement('button');
                buttonSub.type = 'submit';
                buttonSub.className = "btn btn-primary display-4";
                buttonSub.name = "submit_replay_comment";
                buttonSub.textContent = "Post replay now!";
                buttonDiv.appendChild(buttonSub);

                formTag.appendChild(divTextArea);
                formTag.appendChild(buttonDiv);
                formDiv.appendChild(formTag);
                commentSectionTag.appendChild(formDiv);
            }
            else {
                const formDiv = document.createElement('div');
                formDiv.className = "bg-light rounded  p-3 mb-3";
                const formTag = document.createElement('form');
                formTag.action = "community";
                formTag.method = "POST";
                let hiddenInput1 = document.createElement('input');
                hiddenInput1.type = 'hidden';
                hiddenInput1.id="replay-user-id";
                hiddenInput1.value = document.getElementById('content7-i').getAttribute('data-uid');
                hiddenInput1.name = "user";
                let hInput2 = document.createElement('input');
                hInput2.type = "hidden";
                hInput2.id = "post-id-id";
                hInput2.value = idTag.getAttribute('data-post');
                hInput2.name = "post_uuid";
                formTag.appendChild(hiddenInput1);
                formTag.appendChild(hInput2);


                const divTextArea = document.createElement('div');
                divTextArea.className = "col-12 form-group mb-3";
                const textArea = document.createElement('textarea');
                textArea.id = "reply-comment-id";
                textArea.name = "replay_textarea";
                textArea.className = "form-control";
                textArea.placeholder = "Write comment";
                divTextArea.appendChild(textArea);

                const buttonDiv = document.createElement('div');
                buttonDiv.className = "col-lg-12 col-md-12 col-sm-12 align-center mb-lg-5 mbr-section-btn";
                const buttonSub = document.createElement('button');
                buttonSub.type = 'submit';
                buttonSub.className = "btn btn-primary display-4";
                buttonSub.name = "submit_replay_comment";
                buttonSub.textContent = "Post replay now!";
                buttonDiv.appendChild(buttonSub);

                formTag.appendChild(divTextArea);
                formTag.appendChild(buttonDiv);
                formDiv.appendChild(formTag);
                commentSectionTag.appendChild(formDiv);
            }

        }

        const refreshDom = (idTag, data)=>{
            if(data.action === 'likes'){
                idTag.textContent = data.count;
            }
            if (data.action === 'comments'){
                buildCommentSection(data.data, idTag);
            }
        }

        const sendRequests = (data, method, sendIdTag) =>{
            const xhr = new XMLHttpRequest();
            let url ="";

            if(method === "GET"){
                url = baseUrl+'/comment-event-handler?'+data;
            }else{
                url = baseUrl+"/comment-event-handler";
            }
            console.log(url);
            xhr.open(method, url, true);
            xhr.setRequestHeader("Content-Type", 'application/json');
            xhr.onload = function (){
                if(this.status === 200){
                    const data = JSON.parse(this.responseText);
                    refreshDom(sendIdTag, data);
                }
            }

            if(method === "POST"){
                xhr.send(JSON.stringify(data));
            }else{
                xhr.send();
            }
        }

        const likes = (owner, post, idTag) =>{
            if(owner !== null && post !== null){
                const action = 'likes';
                const data = {owner, post, action};
                sendRequests(data, 'POST',idTag);
            }
        }

        const comments = (owner, post, idTag) =>{
            if(owner !== null && post !== null){
                const data = `owner=${owner}&post=${post}&action=comments`;
                sendRequests( data, 'GET',idTag);
            }
        }

        let list = commentId.split(',');
        for (let i = 0; i < list.length; i++){
            document.getElementById('like-post-id-'+list[i]).addEventListener(('click'), (e)=>{
                const thisPost = document.getElementById(e.target.id);
                const owner = thisPost.getAttribute('data-owner');
                const postId = thisPost.getAttribute('data-post');
                likes(owner, postId, thisPost);
            })

            document.getElementById('comment-post-id-'+list[i]).addEventListener(('click'), (e)=>{
                const thisPost = document.getElementById(e.target.id);
                const owner = thisPost.getAttribute('data-owner');
                const postId = thisPost.getAttribute('data-post');
                comments(owner,postId, thisPost);
            })
        }

    </script>
</div>

