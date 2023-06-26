// image preview
$(".image").change(function () {

    if (this.files && this.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.image-preview').attr('src', e.target.result);
        }

        reader.readAsDataURL(this.files[0]);
    }

});
$(".image_tax_card").change(function () {
    if (this.files && this.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.image_tax_card-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    }
});
$(".image_commercial_record").change(function () {
    if (this.files && this.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.image_commercial_record-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    }
});
$(".image_company").change(function () {
    if (this.files && this.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.image_company-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    }
});
