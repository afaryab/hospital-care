<div class="page-content-wrapper">
<div class="page-content">
    <div class="page-bar">
            <ul class="page-breadcrumb">
                <li class="btn btn-default">
                    <a href="<?= site_url($USERS_LIST) ?>">
                        <i class="fas fa-list"></i>
                        <span class="d-inline">Users List</span>
                    </a>
                </li>
                <li class="btn btn-default active">
                    <a>
                        <i class="fas fa-id-badge"></i>
                        <span class="d-inline"><?php echo $user['name']; ?>'s profile</span>
                    </a>
                </li>
            </ul>

        </div>
    <div class="tabbable-line tabbable-full-width">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#tab_1_1" data-toggle="tab" aria-expanded="true"> Overview </a>
            </li>
            <?php if($user['id'] == $currentUser->id){ ?>
            <li class="">
                <a href="#tab_1_3" data-toggle="tab" aria-expanded="false"> Account </a>
            </li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1_1">
                <div class="row">
                    <div class="col-md-3">
                        <ul class="list-unstyled profile-nav">
                            <li>
                                <div class="profileImg" style="display: block;height: 150px;width: 150px;background-position: center;background-size: cover;margin: 0 auto;margin-top: 29px;background-image: url('<?= base_url($user['profile_img_path']) ?>')"></div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-8 profile-info">
                                <h1 class="font-green sbold uppercase"><?= $user['name'] ?></h1>
                                <p class="user-short-story">
                                    <?= $user['short_story'] != ''  ? $user['short_story'] : 'User have not updated his short story'?>
                                </p>
                                <ul class="list-inline">
                                    <li>
                                        <i class="fa fa-map-marker"></i> <?= $user['city'].', '.$user['state'].' ,'.$user['country']; ?> </li>
                                    <li>
                                        <i class="fa fa-briefcase"></i> <?= $user['designation'] ?> </li>
                                    <li>
                                        <i class="fa fa-star"></i> <?= $user['parent_id'] ?> </li>
                                </ul>
                            </div>
                            <!--end col-md-8-->
                            <div class="col-md-4">
                                <div class="portlet sale-summary">
                                </div>
                            </div>
                            <!--end col-md-4-->
                        </div>
                    </div>
                </div>
            </div>
            <!--tab_1_2-->
            <?php if($user['id'] == $currentUser->id){ ?>
            <div class="tab-pane" id="tab_1_3">
                <div class="row profile-account">
                    <div class="col-md-3">
                        <ul class="ver-inline-menu tabbable margin-bottom-10">
                            <li class="active">
                                <a data-toggle="tab" href="#tab_1-1" aria-expanded="false">
                                    <i class="fa fa-cog"></i> Personal info </a>
                                <span class="after"> </span>
                            </li>

                            <li>
                                <a data-toggle="tab" href="#tab_2-2" aria-expanded="false">
                                    <i class="fa fa-picture-o"></i> Change Profile Picture </a>
                            </li>

                        </ul>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">
                            <div id="tab_1-1" class="tab-pane active">
                                <form role="form" method="post">
                                    <div class="form-group">
                                        <label class="control-label">Name</label>
                                        <input type="text" placeholder="<?= $user['name'] ?>" class="form-control" name="first_name"> </div>
                                    <div class="form-group">
                                        <label class="control-label">Mobile Number</label>
                                        <input type="text" placeholder="<?= $user['phone'] ?>" class="form-control" name="phone"> </div>
                                    <div class="form-group">
                                        <label class="control-label">City</label>
                                        <input type="text" placeholder="<?= $user['city'] ?>" class="form-control" name="city"> </div>
                                    <div class="form-group">
                                        <label class="control-label">State</label>
                                        <input type="text" placeholder="<?= $user['state'] ?>" class="form-control" name="state"> </div>
                                    <div class="form-group">
                                        <label class="control-label">Country</label>
                                        <input type="text" placeholder="<?= $user['country'] ?>" class="form-control" name="country"> </div>
                                    <div class="form-group">
                                        <label class="control-label">About</label>
                                        <textarea class="form-control" rows="3" placeholder="<?= $user['short_story'] ?>" name="short_story"></textarea>
                                    </div>
                                    <div class="margiv-top-10">
                                        <button type="submit" class="btn green"> Save Changes </button>
                                    </div>
                                </form>
                            </div>
                            <div id="tab_2-2" class="tab-pane">
                                <form action="#" role="form">
                                    <div class="form-group">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" id="dropzone" style="width: 190px; height: 200px; border: 2px dotted #ccc; background-color: #f3f3f3;background-size: cover;background-position: center;"> </div>

                                        </div>
                                        <div class="clearfix margin-top-10">
                                            <span class="label label-danger"> NOTE! </span>
                                            <span>&nbsp;&nbsp;Attached image thumbnail is supported in Latest Firefox, Chrome, Opera, Safari and Internet Explorer 10 only </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--end col-md-9-->
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<link href="<?= base_url('assets/plugins/dropzone/dropzone.min.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?= base_url('assets/plugins/dropzone/dropzone.js') ?>" ></script>
<script type="text/javascript">
    window.onload = function() {
        DropZoneUpload();
    };

    function dropzoneExists(selector) {
        var elements = $(selector).find('.dz-default');
        return elements.length > 0;
    }

    function DropZoneUpload(){

        var exists = dropzoneExists('div#dropzone');
        if(exists) {
            Dropzone.forElement("div#dropzone").destroy();
        }
        var myDropzone = new Dropzone("div#dropzone", { url: "<?= site_url($CHANGE_PICTURE) ?>"});
        myDropzone.on("success", function(file, response) {

            var node = document.getElementById('dropzone');
            while (node.hasChildNodes()) {
                node.removeChild(node.firstChild);
            }

            if(response.success == 1) {

                document.getElementById('dropzone').style.backgroundImage = 'url('+response.data.path+')';
                updateProfileImage(response.data.path);
                successMsg(response.message, 'It might take some time to effect on all places.');

            }else{
                if(response.action_code == 401) {
                    errorMsg(response.message,'Your session is expired, will be shortly redirected to login screen.');
                    setTimeout(
                        function(){
                            window.location = "<?= site_url('Auth/Login') ?>"
                        },
                        100);
                }else if(response.action_code == 505){
                    errorMsg(response.message,'Please contact administrator or service provider to report this issue.');
                }
            }
        });
    }
</script>
</div>