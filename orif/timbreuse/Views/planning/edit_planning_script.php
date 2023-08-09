<script>
window.onload = function() {
    const form = document.querySelector('form'); 
    //let rate = getRate(formData);
    //showDebug(rate);
    const inputs = document.querySelectorAll('input');
    for(input of inputs) {
        input.onblur = updateInputRate;
        input.onmouseout = updateInputRate;
    }
    updateInputRate();

    //test();
}

async function test() {
    let form = document.querySelector('form'); 
    let formData = new FormData(form);
    alert(await invokeRequestRate(formData));
}

async function invokeRequestRate(formData) {
    const url = '<?= base_url('Plannings/get_rate')?>';
    let request = {};
    request['method'] = 'POST';
    request['body'] = formData;
    let reponse =  await fetch(url, request);
    let reponseText = reponse.text();
    return reponseText;
}

async function getRate(formData) {
    let reponseText = await invokeRequestRate(formData);
    let json = reponseText.split('\n')[0];
    let rate = JSON.parse(json)['rate'];
    return rate;
}

async function updateInputRate() {
    let form = document.querySelector('form'); 
    let formData = new FormData(form);
    let rate = await getRate(formData);
    let rateInput = document.querySelector('#rate');
    rateInput.value = rate;
}

async function showDebug(rate) {
    alert(await rate);
}
</script>
