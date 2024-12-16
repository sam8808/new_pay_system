
<template>
        <button class="tickets-toggler" @click.prevent="handleToggler">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Google_Messages_logo.svg/600px-Google_Messages_logo.svg.png?20221102143805" alt="">
        </button>
        <div class="ticket-wrapper show">
            <div v-if="!isAdmin"  class="ticket-body">
                <button @click="handleFlip" data-flip=".ticket-wrapper">Мои былеты</button>
                <div v-if="ticket instanceof Array && ticket.length" class="user-tickets">
                    <ul>
                        <li class="ticket-card user__mine-tickets" v-for="(item) in ticket" :key="item.id" :data-chat="item.id" @click="handleSetChaT">
                            <span class="subject-item">
                                Subject: {{item.subject}}
                            </span>
                            <span class="status">Status : {{item.status}}</span>
                        </li>
                    </ul>
                </div>
                <form action=""  @submit.prevent="handleForm" id="ticketForm">
                    <textarea name="subject" id="" placeholder="Тема" cols="30" rows="10"></textarea>
                    <label for="ticketCategory">
                        <span>Выберите категорию</span>
                    </label>
                    <select name="category" id="ticketCategory">
                        <option value="technical">Технический</option>
                        <option value="financial">Финансовый</option>
                        <option value="account">Счет</option>
                        <option value="wallet">Кошелек</option>
                        <option value="other">Другой</option>
                    </select>
                    <button class="inset-0 bg-gradient-to-r from-indigo-600 via-violet-600 to-blue-600 group-hover:opacity-100 transition-opacity">Отправить</button>
                </form>
            </div>
            <div v-else class="admin-tickets">
                <div class="tickets-face">
                    <span>Waiting list</span>
                    <ul class="waiting-ticket-list">
                        <li class="ticket-card" v-for="(item,index) in ticket.waiting ?? []" :key="index" @click="hanndleAssign(item.id)">
                            <span>User: {{item.user.email}}</span>
                            <span> Category: {{ item.category}}</span>
                            <span> Subject: {{item.subject}}</span>
                        </li>
                    </ul>
                </div>
                <div class="ticket-back">
                    <span>Mine list</span>
                    <ul class="waiting-ticket-list" v-if="(ticket.mine ?? []).length">
                        <li class="ticket-card" @click="handleSetChaT" :data-chat="item.id" v-for="(item,index) in ticket.mine ?? []" :key="index">
                            <span>User: {{item.user?.email}}</span>
                            <span> Category: {{ item.category}}</span>
                            <span> Subject: {{item.subject}}</span>
                        </li>
                    </ul>
                    <div v-else>
                        Mine list is empty
                    </div>
                </div>

            </div>
        </div>
        <div class="chat-wrapper">
            <div class="chat-head">
                <button class="back-btn" @click="handleRemoveChat">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="256" height="256" viewBox="0 0 256 256" xml:space="preserve">
                    <defs>
                    </defs>
                                            <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                        <path d="M 65.75 90 c 0.896 0 1.792 -0.342 2.475 -1.025 c 1.367 -1.366 1.367 -3.583 0 -4.949 L 29.2 45 L 68.225 5.975 c 1.367 -1.367 1.367 -3.583 0 -4.95 c -1.367 -1.366 -3.583 -1.366 -4.95 0 l -41.5 41.5 c -1.367 1.366 -1.367 3.583 0 4.949 l 41.5 41.5 C 63.958 89.658 64.854 90 65.75 90 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                    </g>
                    </svg>
                </button>

                <div class="admin-wrapper">
                    <img src="https://www.w3schools.com/howto/img_avatar.png" alt="">
                    <div>
                        <span>Admin name:</span>
                        <span>Vlad</span>
                    </div>
                </div>
            </div>
            <div class="chat-body">
                <ul>
                    <li class="message-item" v-for="(message,index) in this.messages" :key="index">
                        {{message.message}}
                    </li>
                </ul>
            </div>
            <div class="chat-footer">

                    <textarea name="" @keydown.enter="handleEnter"
                              @keydown.ctrl.enter="insertLineBreak" v-model="message"
                              placeholder="message..." id=""  rows="1"></textarea>
                <input type="file" multiple @change="handleFileUpload">
                    <button @click="createMessage">Send</button>
            </div>
        </div>

</template>

<style scoped>
    .ticket-wrapper{
        --padding : 25px;
        perspective: 1000px;
        transition: .3s all linear;
        display: grid;
        grid-template-rows: 0;
        &.backface{
            .ticket-body{
                transform: rotateY(180deg);
            }
        }
        &.show{
            grid-template-rows: 1fr;
            .ticket-body{
                opacity: 1;
                pointer-events: all;
                width: auto;
                padding: var(--padding);
            }

        }
        .ticket-card{
            &.user__mine-tickets{
                display: grid;
                gap: unset;
                width: 100%;
                border: 2px solid black ;
                margin-bottom: 12px;
                cursor: pointer;

            }
        }
        .ticket-body{
            transition: .3s all linear;
            opacity: 0;
            min-height: 0;
            pointer-events: none;
            background-color: white;
            transform-style: preserve-3d;
        }
        form{
            display: flex;
            row-gap: 12px;
            flex-direction: column;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            input, textarea, select{
                border-radius: 8px;
            }
            button{
                color: white;
                padding: 12px 6px;
                border-radius: 8px;
            }
        }
        .user-tickets{
            position: absolute;
            inset: var(--padding);
            top: 45px;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform: rotateY(180deg);
        }

    }
    .tickets-toggler{
        width: 50px;
        height: auto;
        border-radius: 50%;
        cursor: pointer;
    }
    .ticket-card{
        display: grid;
        gap: 25px;
        li{
            display: flex;
            flex-wrap: wrap;
            text-align: center;
            span{
                flex: 1 1 100%;
            }
        }

    }
    .waiting-ticket-list{
        display: grid;
        gap: 12px;
        max-height:  270px;
        overflow: scroll;
        .ticket-card{
            border: 1px solid black;
            cursor: pointer;
        }
    }
    .chat-wrapper{
        position: absolute;
        inset: 0;
        background-color: white;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        padding: 12px;
        opacity: 0;
        pointer-events: none;

        &.show{
            opacity: 1;
            pointer-events: all;
        }
        .chat-body{
            flex: 1;
        }
        .chat-head{
            display: flex;
            justify-content: center;
            border-bottom: 1px solid black;
            position: relative;
        }
        .chat-footer{

        }
        .admin-wrapper{
            img{
                margin: 12px auto 12px;
                max-width: 50px;
                max-height: 50px;
                border-radius: 50%;

            }
        }
        .back-btn{
            position: absolute;
            left: 0;
            top: 0;
            svg{
                max-width: 15px;
                max-height: 25px;
            }
        }
    }
</style>
<script>
    import Pusher from 'pusher-js';
    // Pusher.logToConsole = true;

    let pusher = new Pusher('44ad2638c84f29a1cf6a', {
        cluster: 'ap2'
    })

    let channel = pusher.subscribe('my-channel');


    export default {
        name: "Tickets",
        props : {
            isAdmin: {
                type: Boolean,
                default: false
            },
        },
       data(){
            return {
                "message" : '',
                "messages" : [],
                'chatId' : null,
                'ticket' : [],
                'attachments' : [],
            }
       },
        methods : {
            async handleForm(e){
                const data = new FormData(e.target);
                const json = {};
                data.forEach((value, key)=>{
                    json[key] = value;
                })
                let result = await fetch(`http://${window.location.host}/api/tickets/create`, {
                    method: 'POST',
                    headers: {
                        'Content-type' : 'application/json',
                    },
                    body : JSON.stringify(json)
                })
                result = await result.json();
                this.ticket.push(result.ticket)
                e.target.reset();

            },
            handleToggler(){
                this.$el.parentElement.querySelector('.ticket-wrapper').classList.toggle('show')
            },
            handleFlip(){
                this.$el.parentElement.querySelector('.ticket-wrapper').classList.toggle('backface')
            },
            handleEnter(event) {
                if (!event.ctrlKey) {
                    event.preventDefault();
                    this.createMessage();
                }
            },
            insertLineBreak(event) {
                const textarea = event.target;
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;

                this.message =
                    this.message.substring(0, start) + "\n" + this.message.substring(end);

                this.$nextTick(() => {
                    textarea.selectionStart = textarea.selectionEnd = start + 1;
                });
            },
            handleSetChaT(e){
                let element = e.target.closest('li.ticket-card') || (e.target.classList.contains('ticket-card') ? e.target : null)
                if(element){
                    let chatId = element.getAttribute('data-chat');
                    if(chatId){
                        this.setChat(chatId);
                    }
                }

            },
            handleRemoveChat(e){
                let chat = e.target.closest('.chat-wrapper')
                this.chatId = null;
                chat.classList.remove('show')
            },
            hanndleAssign(id){
                let ticket = this.ticket.waiting.splice(
                    this.ticket.waiting.findIndex(item=>item.id===id),1
                )[0]
                fetch(`${window.location.origin}/api/tickets/assign`, {
                    body: JSON.stringify({id: id}),
                    method: "PUT",
                    headers: {
                        'Content-type': 'application/json'
                    }
                }).then(result=>{
                    if(result.ok){
                        this.ticket.mine.push(ticket);
                        this.setChat(id);
                    }
                })

            },
            async createMessage() {
                if (this.message.trim() !== "" || this.attachments ) {
                    const fd = new FormData();
                    this.attachments.forEach((file, index) => {
                        fd.append(`files[${index}]`, file);
                    })
                    fd.append('message', this.message);
                    fd.append('ticket_id' , this.chatId);

                    let response = await fetch(`${window.location.origin}/api/tickets/messages/create`, {
                        method: "POST",
                        body : fd,
                    })
                    this.message = "";
                    let result = await response.json();
                    if(result.ok){
                        this.messages.push(result.messageObject);
                        this.sendMessage(result.messageObject);
                    }
                }

            },
            sendMessage(message){
                fetch(`${window.location.origin}/api/tickets/messages/send`, {
                    body: JSON.stringify({message: message}),
                    method: 'POST',
                    headers: {
                        'Content-type': 'application/json'
                    }
                }).then(result=>{
                    if(!result.ok){
                        console.log(result)
                    }
                })
            },
            async getChatMessages(id) {
                let response = await fetch(`${window.location.origin}/api/tickets/${id}`);
                return await response.json();
            },
            async setChat(id){
                let chatWrapper = document.querySelector('.ticket-sidebar .chat-wrapper');
                this.messages = await this.getChatMessages(id);
                chatWrapper.classList.add('show');
                this.chatId = id;
            },
            handleFileUpload(event){
                this.attachments = Array.from(event.target.files);
            },
        },

        async mounted() {
            fetch(`${window.location.origin}/api/tickets/`).then(result=>{
                result.json().then(data=>{
                    this.ticket = data;
                    console.log(data)

                })
            })

            channel.bind('my-event', (data)=>{
                if (
                    data &&
                    data.message &&
                    data.message.id && // Проверяем, что `id` существует
                    !this.messages.some((msg) => parseInt(msg.id) === parseInt(data.message.id))
                ) {
                    this.messages.push(data.message); // Добавляем сообщение, если его нет
                }
            })
        }
    }

</script>
