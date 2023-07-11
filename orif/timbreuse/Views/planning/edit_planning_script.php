<script>

let form = document.querySelector('form'); 
let formData = new FormData(form);
// alert(formData.get('dueHoursMonday')); 


//let form = document.querySelector('form').elements; 

// alert(json);

//let o = {};
//for (const i in form) {
//    o[form[i].name] = form[i].value;
//}

//let json = JSON.stringify(o);
// let jsonEncode = encodeURIComponent(json);

//     fetch("<?= base_url('migration/init')?>", { method:'POST', body:formdata,})


async function test(formData) {
    let url = '<?= base_url('Plannings/test2')?>';
    let request = {};
    request['method'] = 'POST';
    request['body'] = formData;
    // header['method'] = 'POST';
    // header['mode'] = 'cors';
    // header['cache'] = 'no-cache';
    // header['credentials'] = 'same-origin';
    // header['headers'] = {};
    // header['headers']['Content-Type'] = 'application/json';
    // header['redirect'] = 'follow';
    // header['referrerPolicy'] = 'no-referrer';
    // header['body'] = json;

    //header['body'] = JSON.stringify({a: 2, b:3});
    // header = {method:'POST', body:json};
    // let formdata = new FormData();
    // formdata.append('a', 'b');
    // header['body'] = formdata;
    //let reponse =  await fetch("<?= base_url('Plannings/test2')?>", header);
    let reponse =  await fetch(url, request);
    let reponseText = await reponse.text();
    //return await reponse.text();
    return reponseText.split('\n')[0];
}
let rate = test(formData);

async function test2(rate) {
    alert(await rate);
}
test2(rate);
</script>
