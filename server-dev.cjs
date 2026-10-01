/* Lightweight Windows/XAMPP fallback when `php artisan serve` is unavailable. */
const http=require('http'),fs=require('fs'),path=require('path'),{spawn}=require('child_process');
const root=__dirname,publicDir=path.join(root,'public'),php='C:\\xampp\\php\\php-cgi.exe';
const mime={'.css':'text/css','.js':'application/javascript','.svg':'image/svg+xml','.png':'image/png','.jpg':'image/jpeg','.ico':'image/x-icon'};
http.createServer((req,res)=>{
  const pathname=decodeURIComponent(new URL(req.url,'http://localhost').pathname),candidate=path.normalize(path.join(publicDir,pathname));
  if(candidate.startsWith(publicDir)&&fs.existsSync(candidate)&&fs.statSync(candidate).isFile()){
    res.writeHead(200,{'Content-Type':mime[path.extname(candidate)]||'application/octet-stream'});return fs.createReadStream(candidate).pipe(res);
  }
  const chunks=[];req.on('data',c=>chunks.push(c));req.on('end',()=>{
    const body=Buffer.concat(chunks),env={...process.env,REDIRECT_STATUS:'200',GATEWAY_INTERFACE:'CGI/1.1',SERVER_PROTOCOL:'HTTP/1.1',REQUEST_METHOD:req.method,REQUEST_URI:req.url,QUERY_STRING:new URL(req.url,'http://localhost').search.slice(1),SCRIPT_FILENAME:path.join(publicDir,'index.php'),SCRIPT_NAME:'/index.php',DOCUMENT_ROOT:publicDir,SERVER_NAME:'127.0.0.1',SERVER_PORT:'8017',HTTP_HOST:req.headers.host||'127.0.0.1:8017',HTTP_ACCEPT:req.headers.accept||'*/*',HTTP_COOKIE:req.headers.cookie||'',HTTP_X_CSRF_TOKEN:req.headers['x-csrf-token']||'',CONTENT_TYPE:req.headers['content-type']||'',CONTENT_LENGTH:String(body.length)};
    const child=spawn(php,[],{cwd:root,env});let output=Buffer.alloc(0),errors='';child.stdout.on('data',d=>output=Buffer.concat([output,d]));child.stderr.on('data',d=>errors+=d);child.on('close',()=>{
      const marker=output.indexOf('\r\n\r\n'),rawHeaders=output.subarray(0,marker).toString(),payload=output.subarray(marker+4),headers={};let status=200;
      for(const line of rawHeaders.split('\r\n')){const i=line.indexOf(':');if(i<0)continue;const key=line.slice(0,i),value=line.slice(i+1).trim();if(key.toLowerCase()==='status')status=parseInt(value)||200;else if(key.toLowerCase()==='set-cookie'){const current=headers['Set-Cookie']||[];current.push(value);headers['Set-Cookie']=current;}else headers[key]=value;}
      if(marker<0){status=500;headers['Content-Type']='text/plain';return res.writeHead(status,headers).end(errors||output);}
      res.writeHead(status,headers);res.end(payload);
    });child.stdin.end(body);
  });
}).listen(8017,'127.0.0.1',()=>console.log('Grid Dominion preview: http://127.0.0.1:8017'));
