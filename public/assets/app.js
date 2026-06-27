const API = '../php/api/';
const state = { token: localStorage.nexchat_token || '', user: null, conversationId: null, peer: null };
const $ = id => document.getElementById(id);
const call = async (path, body, method = 'POST') => {
  const res = await fetch(API + path, { method, headers: {'Content-Type':'application/json', ...(state.token ? {Authorization:`Bearer ${state.token}`} : {})}, body: body ? JSON.stringify(body) : undefined });
  const json = await res.json();
  if (!res.ok) throw new Error(json.error || 'Erreur API');
  return json;
};
const renderMsg = m => `<div class="message ${m.sender_id == state.user?.id ? 'mine' : ''} ${m.message_type === 'ai' ? 'ai' : ''}"><div class="meta">${m.sender_name || 'Moi'} · ${new Date(m.created_at).toLocaleTimeString()}</div>${escapeHtml(m.body)}</div>`;
const escapeHtml = s => (s || '').replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
async function boot(){ if(!state.token) return; try{ const me = await call('auth.php?action=me', null, 'GET'); state.user = me.user; $('auth').classList.add('hidden'); $('appNav').classList.remove('hidden'); $('presence').textContent = `Connecté: ${me.user.display_name}`; initPeer(); await load(); }catch{ localStorage.removeItem('nexchat_token'); }}
async function load(){ const data = await call(`messages.php${state.conversationId ? `?conversation_id=${state.conversationId}` : ''}`, null, 'GET'); $('conversations').innerHTML = data.conversations.map(c=>`<button data-cid="${c.id}">${escapeHtml(c.title || 'Discussion')}</button>`).join(''); $('messages').innerHTML = data.messages.length ? data.messages.map(renderMsg).join('') : $('messages').innerHTML; document.querySelectorAll('[data-cid]').forEach(b=>b.onclick=()=>{state.conversationId=b.dataset.cid; $('roomTitle').textContent=b.textContent; load();}); }
async function initPeer(){ if(!window.Peer) return; state.peer = new Peer(undefined, { host:'nexchat.alwaysdata.net', path:'/peerjs', secure:true }); state.peer.on('open', id => call('peer.php', { peer_id:id }).catch(console.warn)); state.peer.on('call', mediaCall => navigator.mediaDevices.getUserMedia({audio:true,video:false}).then(stream=>mediaCall.answer(stream))); }
$('loginBtn').onclick = async()=>{ const r=await call('auth.php?action=login',{login:$('login').value,password:$('password').value}); localStorage.nexchat_token=state.token=r.token; location.reload(); };
$('registerBtn').onclick = async()=>{ const r=await call('auth.php?action=register',{username:$('username').value,display_name:$('displayName').value,email:$('email').value,password:$('password').value}); localStorage.nexchat_token=state.token=r.token; location.reload(); };
$('searchBtn').onclick = async()=>{ const r=await call(`messages.php?action=contacts&q=${encodeURIComponent($('contactSearch').value)}`, null, 'GET'); $('contacts').innerHTML=r.contacts.map(c=>`<button data-uid="${c.id}">＋ ${escapeHtml(c.display_name)} @${escapeHtml(c.username)}</button>`).join(''); document.querySelectorAll('[data-uid]').forEach(b=>b.onclick=async()=>{ const x=await call('messages.php?action=start',{user_id:b.dataset.uid}); state.conversationId=x.conversation_id; await load(); }); };
$('composer').onsubmit = async e=>{ e.preventDefault(); if(!state.conversationId) return alert('Choisis ou crée une discussion'); await call('messages.php?action=send',{conversation_id:state.conversationId,body:$('messageInput').value,client_id:crypto.randomUUID()}); $('messageInput').value=''; await load(); };
$('askAiBtn').onclick = async()=>{ $('aiAnswer').textContent='NexIA réfléchit...'; const r=await call('ai.php',{prompt:$('aiPrompt').value}); $('aiAnswer').textContent=r.reply; };
$('aiBtn').onclick = () => { $('aiPrompt').value = `Propose une réponse courte et chaleureuse pour cette conversation.`; $('askAiBtn').click(); };
$('callBtn').onclick = () => alert('Appels PeerJS prêts: récupère le peer_id du contact et lance peer.call(peerId, stream) côté UI avancée.');
boot();
