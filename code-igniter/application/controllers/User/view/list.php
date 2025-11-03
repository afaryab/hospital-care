
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar subBar">
            <ul class="page-breadcrumb">
                <li class="btn btn-default active">
                    <a href="<?= site_url($USERS_LIST) ?>">
                        <i class="fas fa-list"></i>
                        <span class="d-inline"><?php echo $title; ?></span>
                    </a>
                </li>
            </ul>
            <ul class="page-breadcrumb pull-right">
                <?php if($this->aauth->is_allowed('Create User', 'Users Management')){ ?>
                    <li class="btn btn-primary pull-right">
                        <a href="<?= site_url($CREATE_USER) ?>" >
                            <i class="fas fa-user-plus"></i>
                            <span class="d-inline">Create New User</span>
                        </a>
                    </li>
                <?php } ?>
            </ul>

        </div>
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            
            <div class="col-md-12 ">
                <!-- BEGIN SAMPLE FORM PORTLET-->
                <div class="portlet light bordered">
                    <div class="portlet-body form">
                        <table id="example" class="display" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Account</th>
                                <th>Login Information</th>
                                <th>Activities</th>
                                <th>Payrol</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Account</th>
                                <th>Login Information</th>
                                <th>Activities</th>
                                <th>Payrol</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            <?php
                            if(!empty($users)){
                                foreach ($users as $row) {
                                    if($row->id != 1) {
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="user-shrt-profile-img"> 
                                                    <img alt="" class="img-circle profileImg" src="<?= base_url($row->profile_img_path) ?>">
                                                </div>
                                                <div class="user-shrt-profile-text">
                                                    <a href="<?= site_url($PROFILE_USER.$row->id) ?>" title="<?= $row->name ?>'s Profile">
                                                        <strong><?= $row->name ?></strong>
                                                    </a><br/>
                                                    <a href="javasccript:void(0)" title="<?= $row->name ?>'s Designation">
                                                        <?= $row->designation == '' ? 'Designation not defined' : $row->designation ?>
                                                    </a><br/>
                                                    <a href="phone:<?= $row->phone ?>" title="Call <?= $row->name ?>">
                                                        <i class="fas fa-phone-square"></i> <?= $row->phone == '' ? 'Contact not found' : $row->phone ?>
                                                    </a><br/>
                                                    <a href="address:<?= ($row->address == '' ? '' : $row->address).' '.($row->city == '' ? '' : $row->city).' '.($row->state == '' ? '' : $row->state).' '.($row->country == '' ? 'Pakistan' : $row->country).'.' ?>" title="<?= $row->name ?>'s Address">
                                                        <i class="fas fa-map-marked-alt"></i> <?= ($row->address == '' ? '' : $row->address).' '.($row->city == '' ? '' : $row->city).' '.($row->state == '' ? '' : $row->state).' '.($row->country == '' ? 'Pakistan' : $row->country) ?>
                                                    </a><br/>
                                                    <a href="mailto:<?= $row->communication_email ?>"><i class="fas fa-at"></i><?= $row->communication_email == '' ? 'not defined' : $row->communication_email ?></a><br/>
                                                </div>
                                                <a class="btn btn-default user-shrt-profile-pull-right btn-sm pull-right" href="<?= site_url($EDIT_USER.$row->id) ?>" title="Edit <?= $row->name ?>"><i class="fas fa-user-edit"></i></a>
                                                <a class="btn btn-default user-shrt-profile-pull-right btn-sm pull-right" href="<?= site_url($PROFILE_USER.$row->id) ?>" title="<?= $row->name ?>'s Profile"><i class="fas fa-id-badge"></i></a>
                                            </td>
                                            <td> 
                                                <div class="user-shrt-profile">
                                                    <a href="javasccript:void(0)" title="<?= $row->name ?>'s Type">
                                                        Type: <?= $row->is_receptionist == 1 ? "Receptionist" : ($row->is_super_admin == 1 ? "Administrator" : ($row->is_doctor == 1 ? "Doctor" : ($row->is_nurse == 1 ? "Nurse" : "Business User")))  ?>
                                                    </a><br/>
                                                    <a href="javasccript:void(0)" title="<?= $row->name ?>'s Status: <?= $row->banned == 1 ? "Blocked due to ".$row->banned_message : "Active"  ?>">
                                                        Status: <?= $row->banned == 1 ? "Blocked " : "Active"  ?>
                                                    </a><br/>
                                                    <a title="Member Since: <?= date('Y-m-d h:i:s a', strtotime($row->created_on)) ?>">Member Since: <?= nicetime(date('Y-m-d h:i:s a', strtotime($row->created_on))) ?></a><br/>
                                                    <a title="Last Modified: <?= date('Y-m-d h:i:s a', strtotime($row->modified_on)) ?>">Last Modified: <?= nicetime(date('Y-m-d h:i:s a', strtotime($row->modified_on))) ?></a>
                                                </div>
                                                <?php if($row->banned == 0 ){ ?>
                                                    <a class="btn btn-danger btn-sm pull-right" onclick="<?php if($currentUser->id == $row->id){ echo "return confirm('You cannot Block Your own account.');"; }else{ echo "return blockAccount('".$row->name."', '".$row->id."')"; } ?>" title="<?= ($currentUser->id == $row->id) ? 'You cannot Block yourself!' : 'Block'?>"><i class="fas fa-ban"></i></a>
                                                <?php } else{ ?>
                                                    <a class="btn btn-success btn-sm pull-right" onclick="return confirm('Are you sure you want to un block <?= $row->name ?>?');" href="<?= site_url($ALLOW_USER.$row->id) ?>" title="Un Block User"><i class="fas fa-thumbs-up"></i></a>
                                                <?php } ?>
                                            </td>
                                            <td> 
                                                <div class="user-shrt-profile">
                                                    <a href="mailto:<?= $row->email ?>">Login Email: <?= $row->email ?></a><br/>
                                                    
                                                    
                                                    <a title="Last Login: <?= date('Y-m-d h:i:s a', strtotime($row->last_login)) ?>">Last Login: <?= nicetime(date('Y-m-d h:i:s a', strtotime($row->last_login))) ?></a>
                                                </div>
                                                <a class="btn btn-default btn-sm pull-right" onclick="return confirm('Are you sure you want to resend reset email to <?= $row->name ?>?');" href="<?= site_url($RESET_USER.$row->id) ?>" title="Reset Password Email"><i class="fas fa-at"></i></a>
                                                <a class="btn btn-default btn-sm pull-right" onclick="return confirm('Are you sure you want to change password for <?= $row->name ?>?');" href="<?= site_url($CHANGE_USER_PASS.$row->id) ?>" title="Change Password"><i class="fas fa-user-lock"></i></a>
                                            </td>
                                            <td>
                                                <a href="javasccript:void(0)" title="Last IP: <?= $row->name ?>">
                                                    Last IP: <?= $row->ip_address == '' ? '-.-.-.-' : $row->ip_address ?>
                                                </a><br/>
                                                <a title="Last Activity: <?= date('Y-m-d h:i:s a', strtotime($row->last_activity)) ?>">Last Activity: <?= nicetime(date('Y-m-d h:i:s a', strtotime($row->last_login))) ?></a><br/>
                                                
                                            </td>
                                            <td> 
                                                    
                                                <a href="javasccript:void(0)" title="<?= $row->name ?>'s Salary Type">
                                                    Salary type: <?= $row->opd_charges_type == 1 ? 'Services Share' : 'Fixed' ?>
                                                </a><br/>
                                                <a href="javasccript:void(0)" title="<?= $row->name ?>'s Salary Type">
                                                    Salary: <?= $row->salery_amount == '' ? '0' : $row->salery_amount ?>
                                                </a><br/>
                                                <a href="javasccript:void(0)" title="<?= $row->name ?>'s Last Salary">
                                                    Last Salary: <?= $row->salery_amount == '' ? '0' : $row->salery_amount ?>
                                                </a><br/>
                                                <a title="Last Activity: <?= date('Y-m-d h:i:s a', strtotime($row->last_activity)) ?>">Last Activity: <?= nicetime(date('Y-m-d h:i:s a', strtotime($row->last_login))) ?></a><br/>    
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    

<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/plugins/datatables/media/css/jquery.dataTables_themeroller.css') ?>"/>
<script type="text/javascript" src="<?php echo base_url('public/plugins/datatables/all.min.js') ?>"></script>

<script>
    jQuery(function(){
        $('#example').DataTable();
    })

    function blockAccount(name,userId){
        var why = prompt("Why are you blocking this "+name, "His application interaction no longer required.");

        if (why == null || why == "") {
            return false;
        } else {
            var confirmStatus = confirm("Are you sure you want to block "+name+" because "+why);

            if(confirmStatus){
                var url = "<?php echo site_url($BLOCK_USER); ?>";
                url = url + "/" + userId + "?msg="+why;
                window.location = url;

            }
            
            return confirmStatus;

        }
    }
</script>