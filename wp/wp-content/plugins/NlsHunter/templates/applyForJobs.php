<form class="nls-apply-for-jobs w-10/12 mx-auto mt-4" name="nls-apply-for-jobs nls-box-shadow">
    <input type="hidden" name="sid" class="sid-hidden-field" value="<?= $model->nlsGetSupplierId() ?>">
    <div class="form-section">
        <div class="friends-details">
            <div class="form-header w-full mx-2 mb-4">
                <h3 class="form-title font-bold"><?= __('My friend details:', 'NlsHunter') ?></h3>
            </div>
            <div class="form-body flex flex-col">
                <div class="friend flex flex-col md:flex-row flex-wrap">
                    <span class="remove"></span>

                    <!--  NAME -->
                    <?= render('form/nlsInputField', [
                        'wrapperClass' => 'w-full md:w-72 mb-4 mx-2 text-xl',
                        'class' => 'rounded-md px-3 py-2 text-primary w-full',
                        'label' => __('Friend Name', 'NlsHunter'),
                        'name' => 'friend-name[]',
                        'validators' => ['required'],
                        'autofocus' => true
                    ]) ?>

                    <!--  CELL PHONE -->
                    <?= render('form/nlsInputField', [
                        'wrapperClass' => 'w-full md:w-72 mb-4 mx-2 text-xl',
                        'type' => 'tel',
                        'class' => 'rounded-md px-3 py-2 text-primary w-full',
                        'label' => __('Phone', 'NlsHunter'),
                        'name' => 'friend-phone[]',
                        'validators' => ['required', 'phone']
                    ]) ?>

                    <!-- JOB SELECT -->
                    <?= render('form/nlsSelectField', [
                        'wrapperClass' => 'sumo w-full md:w-72 mb-4 mx-2 text-xl',
                        'class' => 'rounded-md px-3 py-2 text-primary',
                        'label' => __('Which Position?', 'NlsHunter'),
                        'labelClass' => '',
                        'name' => 'friend-job-code[]',
                        'placeHolder' => __('Select', 'NlsHunter'),
                        'options' => $jobOptions,
                        'clearAllButton' => true, // For single select
                        'clearAllButtonClass' => 'hidden bg-primary text-white py-1 px-2 mx-1 border border-primary rounded-xl', // For single select
                    ]) ?>

                    <!--  CV FILE -->
                    <?= render('form/nlsFileField', [
                        'wrapperClass' => 'w-full md:w-72 mb-4 mx-2 text-xl',
                        'label' => __('Upload CV', 'NlsHunter'),
                        'name' => 'friend-cv-file[]',
                        'buttonText' => __('Attach cv file (if availiable)', 'NlsHunter'),
                        'accept' => '.txt, .pdf, .doc, .docx, .rtf',
                        'buttonClass' => 'pl-3 py-2 underline',
                        'mode' => 'text',
                        'iconSrc' => plugins_url('NlsHunter/public/images/checkmark.png'),
                        'iconClass' => 'h-6 hidden',
                        'validators' => ['required']
                    ]) ?>
                </div>
            </div>
            <div class="form-footer mx-2 mb-6">
                <button type="button" class="another-friend bg-primary text-white text-4xl px-8 pt-1 pb-2 rounded-md"><?= __('Another Friend', 'NlsHunter') ?></button>
            </div>
        </div>
    </div>
    <div class="form-section mt-6">
        <div class="employee-details">
            <div class="form-header w-full mt-2 mb-4">
                <h3 class="form-title font-bold"><?= __('My details:', 'NlsHunter') ?></h3>
            </div>
            <div class="form-body flex flex-col md:flex-row flex-wrap">

                <!--  NAME -->
                <?= render('form/nlsInputField', [
                    'wrapperClass' => 'w-full md:w-72 mb-4 mx-2 text-xl',
                    'class' => 'rounded-md px-3 py-2 text-primary w-full',
                    'label' => __('Full Name', 'NlsHunter'),
                    'name' => 'employee-name',
                    'validators' => ['required'],
                ]) ?>

                <!--  CELL PHONE -->
                <?= render('form/nlsInputField', [
                    'wrapperClass' => 'w-full md:w-72 mb-4 mx-2 text-xl',
                    'type' => 'tel',
                    'class' => 'rounded-md px-3 py-2 text-primary w-full',
                    'label' => __('Cell Phone', 'NlsHunter'),
                    'name' => 'employee-phone',
                    'validators' => ['required', 'phone']
                ]) ?>

                <!--  ID -->
                <?= render('form/nlsInputField', [
                    'wrapperClass' => 'w-full md:w-72 mb-4 mx-2 text-xl',
                    'type' => 'numeric',
                    'class' => 'rounded-md px-3 py-2 text-primary w-full',
                    'label' => __('ID', 'NlsHunter'),
                    'name' => 'employee-id',
                    'validators' => ['required', 'ISRID']
                ]) ?>

            </div>
            <div class="form-footer mx-2">
                <button type="button" class="apply-job bg-success text-white text-4xl w-full md:w-72 pt-1 pb-2 rounded-md"><?= __('Send', 'NlsHunter') ?></button>
            </div>
        </div>
    </div>
</form>