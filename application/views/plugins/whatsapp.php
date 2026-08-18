<div class="card card-block">

    <div id="notify" class="alert alert-success" style="display:none;">

        <a href="#" class="close" data-dismiss="alert">&times;</a>



        <div class="message"></div>

    </div>

    <form method="post" id="data_form" class="form-horizontal">

        <div class="card-body">



            <h5>Whatsapp API Service</h5>

            <hr>





            <p>You can send bills as Whatsapp to your customers using Dialtext Whatsapp Service. You can also setup urls

                shorter plugin to convert long invoice urls to small and more user friendly in Whatsapp.</p>

            <p>You can signup here for keys. <a href="https://dialtext.com"> https://dialtext.com/index.php</a></p>



            <div class="form-group row">





                <div class="col-sm-6"><label class="col col-form-label" for="terms">USERNAME</label>

                    <input type="text"

                           class="form-control margin-bottom  required" name="key1"

                           value="<?php echo $universal['key1'] ?>">

                </div>





                <div class="col-sm-6"><label class="col col-form-label" for="terms">PASSWORD</label>

                    <input type="text"

                           class="form-control margin-bottom  required" name="key2"

                           value="<?php echo $universal['key2'] ?>">

                </div>

            </div>



            <div class="form-group row">





                <div class="col-sm-6"><label class="col col-form-label" for="terms">URL</label>

                    <input type="text"

                           class="form-control margin-bottom  required" name="sender"

                           value="<?php echo $universal['url'] ?>">

                </div>





                <div class="col-sm-6"><label class="col col-form-label"

                                             for="terms">Auto Enable</label>

                    <select name="enable" class="form-control">



                        <?php switch ($universal['active']) {

                            case 1 :

                                echo '<option value="1">--Yes--</option>';

                                break;

                            case 0 :

                                echo '<option value="0">--No--</option>';

                                break;



                        } ?>

                        <option value="1">Yes</option>

                        <option value="0">No</option>





                    </select>

                </div>

            </div>





            <div class="form-group row">





                <div class="col-sm-4">

                    <input type="submit" id="submit-data" class="btn btn-success margin-bottom"

                           value="<?php echo $this->lang->line('Update') ?>" data-loading-text="Updating...">

                    <input type="hidden" value="plugins/whatsapp" id="action-url">

                </div>

            </div>


        </div>

    </form>



</div>