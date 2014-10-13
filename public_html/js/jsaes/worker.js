importScripts("jsaes.js");

function str2array(str, len)
{
    var l = (Math.floor((str.length+len-1)/len))*len;
    var ret=new Array(l);
    for(var i=0;i<l;i++)
    {
        if(i<str.length)
         ret[i]=str.charCodeAt(i);
        else ret[i]=32;
    }
    return ret;
}
function EncryptArray(data, pass, len)
{
    var ilen = data.byteLength;
    var l = (Math.floor((ilen+len-1)/len))*len;
    
    var oarray = new ArrayBuffer(l+4);
    var lenbuf = new Uint32Array(oarray);
    var obuf = new Uint8Array(oarray, 4);
    var ibuf = new Uint8Array(data);
    
    lenbuf[0]=ilen;
    
    var prg = l / 10;
    var prgi = 0;
    var prgp = 0;

    
    var key=str2array(pass,len);
    AES_Init();
    AES_ExpandKey(key);
    
    for(var i=0;i<l;i+=16)
    {
        var block = new Array(16);
        for(var j=0;j<16;j++)
        {
            if((i+j)>ibuf.length)
                block[j]=32;
            else
                block[j]=ibuf[i+j];
            if(prgi>=prg)
            {
                prgi=0;
                prgp+=10;
                self.postMessage({cmd:1,prg:"Encrypting ... "+prgp+"%"});
            }
            prgi++;
        }
        AES_Encrypt(block, key);
        for(var j=0;j<16;j++)
            obuf[i+j]=block[j];
    }
    AES_Done();
    return oarray;
}
function DecryptArray(data, pass, len)
{
    var lenbuf = new Uint32Array(data);
    var oarray = new ArrayBuffer(lenbuf[0]);
    var obuf = new Uint8Array(oarray);
    var ibuf = new Uint8Array(data, 4);

    var key=str2array(pass,len);

    var prg = ibuf.length / 10;
    var prgi = 0;
    var prgp = 0;
    
    AES_Init();
    AES_ExpandKey(key);
    for(var i=0;i<ibuf.length;i+=16)
    {
        var block = new Array(16);
        for(var j=0;j<16;j++)
            block[j]=ibuf[i+j];
        AES_Decrypt(block, key);
        for(var j=0;j<16;j++)
        {
            if((i+j)<obuf.length)
                obuf[i+j]=block[j];
            if(prgi>=prg)
            {
                prgi=0;
                prgp+=10;
                self.postMessage({cmd:1,prg:"Decrypting ... "+prgp+"%"});
            }
            prgi++;
        }
    }
    AES_Done();
    return oarray;
}
    

self.addEventListener('message', function(e) {
    if(e.data.cmd == 1)
    {
        var encfile = EncryptArray(e.data.data,e.data.pass, e.data.len);
        delete e.data;
        self.postMessage({cmd:0,data:encfile,msg:"Download encrypted file"});
    }else
    if(e.data.cmd == 2)
    {
        var decfile = DecryptArray(e.data.data,e.data.pass, e.data.len);
        delete e.data;
        self.postMessage({cmd:0,data:decfile,msg:"Download decrypted file"});
    }
});