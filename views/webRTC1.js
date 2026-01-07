'use strict';

app.factory('WebRTC1', function( global ){

  var data = {
    getMediaBtn: true,
    createPeerConnectionBtn: false,
    createOfferBtn: false,
    setOfferBtn: false,
    createAnswerBtn: false,
    setAnswerBtn:false,
    hangUpBtn: false
  };

  
  let dataChannelDataReceived;


  const offerSdpTextarea = document.querySelector('div#local textarea');
  const answerSdpTextarea = document.querySelector('div#remote textarea');

  const audioSelect = document.querySelector('select#audioSrc');
  const videoSelect = document.querySelector('select#videoSrc');

  audioSelect.onchange = videoSelect.onchange = getMedia;
  
  const localVideo = document.querySelector('div#local video');
  const remoteVideo = document.querySelector('div#remote video');

  const selectSourceDiv = document.querySelector('div#selectSource');

  let localPeerConnection;
  let remotePeerConnection;
  let localStream;
  let sendChannel;
  let receiveChannel;
  const dataChannelOptions = {ordered: true};
  let dataChannelCounter = 0;
  let sendDataLoop;

  const offerOptions = {
    offerToReceiveAudio: 1,
    offerToReceiveVideo: 1
  };

    

    

  function onSetSessionDescriptionSuccess() {
    console.log('Set session description success.');
  }

  function onSetSessionDescriptionError(error) {
    console.log(`Failed to set session description: ${error.toString()}`);
  }

  function onCreateSessionDescriptionError(error) {
    console.log(`Failed to create session description: ${error.toString()}`);
  }

    

    


  // ------------- edited files -------------------------

  // ---------------------------------------
  // initialize for availability of devices like audio and camera
  // -------------------------------------------
  async function Init() {
    try {
      const enumerateDevices = await navigator.mediaDevices.enumerateDevices();
      gotSources(enumerateDevices);
    } 
    catch (e) {
      console.log(e);
    }
  }

  // ------------------------------------
  // looking for devices to use and select
  // ------------------------------------------
  function gotSources(sourceInfos) {
    
    selectSourceDiv.classList.remove('hidden');

    let audioCount = 0;
    let videoCount = 0;

    for (let i = 0; i < sourceInfos.length; i++) {
      
      const option = document.createElement('option');

      option.value = sourceInfos[i].deviceId;
      option.text = sourceInfos[i].label;

      if (sourceInfos[i].kind === 'audioinput') {
        audioCount++;

        if (option.text === '') {
          option.text = `Audio ${audioCount}`;
        }
        audioSelect.appendChild(option);
      } 
      else if (sourceInfos[i].kind === 'videoinput') {
        videoCount++;
        if (option.text === '') {
          option.text = `Video ${videoCount}`;
        }
        videoSelect.appendChild(option);
      } 
      else {
        console.log('unknown', JSON.stringify(sourceInfos[i]));
      }
    }
  }


  // --------------------------
  // local setup for initializing the camera and audio
  // ----------------------------
  async function getMedia() {

    data.getMediaBtn = false;
    data.createPeerConnectionBtn = true;

    if (localStream) {
      localVideo.srcObject = null;
      localStream.getTracks().forEach(track => track.stop());
    }

    const audioSource = audioSelect.value;
    console.log(`Selected audio source: ${audioSource}`);

    const videoSource = videoSelect.value;
    console.log(`Selected video source: ${videoSource}`);

    const constraints = {
      audio: {
        optional: [{
          sourceId: audioSource
        }]
      },
      video: {
        optional: [{
          sourceId: videoSource
        }]
      }
    };

    console.log('Requested local stream');

    try {
      const userMedia = await navigator.mediaDevices.getUserMedia(constraints);
      gotStream(userMedia);
    } 
    catch (e) {
      console.log('navigator.getUserMedia error: ', e);
    }
  }

  function gotStream(stream) {
    console.log('Received local stream');
    localVideo.srcObject = stream;
    localStream = stream;
  }


  // --------------------------
  // local video starting up
  // ----------------------------
  function createPeerConnection() {


    data.createPeerConnectionBtn = false;
    data.createOfferBtn = true;
    data.createAnswerBtn = true;
    data.setOfferBtn = true;
    data.setAnswerBtn = true;
    data.hangUpBtn = true;

    console.log('Starting call');
    const videoTracks = localStream.getVideoTracks();
    const audioTracks = localStream.getAudioTracks();

    if (videoTracks.length > 0) {
      console.log(`Using video device: ${videoTracks[0].label}`);
    }

    if (audioTracks.length > 0) {
      console.log(`Using audio device: ${audioTracks[0].label}`);
    }
    const servers = null;

    window.localPeerConnection = localPeerConnection = new RTCPeerConnection(servers);

    console.log('Created local peer connection object localPeerConnection');

    localPeerConnection.onicecandidate = e => onIceCandidate(localPeerConnection, e);

    sendChannel = localPeerConnection.createDataChannel('sendDataChannel', dataChannelOptions);

    sendChannel.onopen = onSendChannelStateChange;
    sendChannel.onclose = onSendChannelStateChange;
    sendChannel.onerror = onSendChannelStateChange;

    window.remotePeerConnection = remotePeerConnection = new RTCPeerConnection(servers);
    console.log('Created remote peer connection object remotePeerConnection');
    remotePeerConnection.onicecandidate = e => onIceCandidate(remotePeerConnection, e);
    remotePeerConnection.ontrack = gotRemoteStream;
    remotePeerConnection.ondatachannel = receiveChannelCallback;

    localStream.getTracks()
        .forEach(track => localPeerConnection.addTrack(track, localStream));
    console.log('Adding Local Stream to peer connection');
  }


  function onSendChannelStateChange() {
    const readyState = sendChannel.readyState;
    console.log(`Send channel state is: ${readyState}`);
    if (readyState === 'open') {
      sendDataLoop = setInterval(sendData, 1000);
    } else {
      clearInterval(sendDataLoop);
    }
  }

  function sendData() {

    if (sendChannel.readyState === 'open') {
      sendChannel.send(dataChannelCounter);
      console.log(`DataChannel send counter: ${dataChannelCounter}`);
      dataChannelCounter++;
    }
  }


  async function onIceCandidate(pc, event) {
    try {
      // eslint-disable-next-line no-unused-vars
      const ignore = await getOtherPc(pc).addIceCandidate(event.candidate);
      onAddIceCandidateSuccess(pc);
    } catch (e) {
      onAddIceCandidateError(pc, e);
    }

    console.log(`${getName(pc)} ICE candidate:\n${event.candidate ? event.candidate.candidate : '(null)'}`);
  }

  function getOtherPc(pc) {
    return (pc === localPeerConnection) ? remotePeerConnection : localPeerConnection;
  }

  function onAddIceCandidateSuccess() {
    console.log('AddIceCandidate success.');
  }

  function onAddIceCandidateError(error) {
    console.log(`Failed to add Ice Candidate: ${error.toString()}`);
  }

  function getName(pc) {
    return (pc === localPeerConnection) ? 'localPeerConnection' : 'remotePeerConnection';
  }


  function gotRemoteStream(e) {
    if (remoteVideo.srcObject !== e.streams[0]) {
      remoteVideo.srcObject = e.streams[0];
      console.log('Received remote stream');
    }
  }


  function receiveChannelCallback(event) {
    console.log('Receive Channel Callback');
    receiveChannel = event.channel;
    receiveChannel.onmessage = onReceiveMessageCallback;
    receiveChannel.onopen = onReceiveChannelStateChange;
    receiveChannel.onclose = onReceiveChannelStateChange;
  }

  function onReceiveMessageCallback(event) {
    dataChannelDataReceived = event.data;
    console.log(`DataChannel receive counter: ${dataChannelDataReceived}`);
  }


  function onReceiveChannelStateChange() {
    const readyState = receiveChannel.readyState;
    console.log(`Receive channel state is: ${readyState}`);
  }



  // --------------------------
  // Create offer after local is setup and give information
  // ----------------------------
  async function createOffer() {
    try {
      const offer = await localPeerConnection.createOffer(offerOptions);
      gotDescription1(offer);
    } catch (e) {
      onCreateSessionDescriptionError(e);
    }
  }

  function gotDescription1(description) {
    offerSdpTextarea.disabled = false;
    offerSdpTextarea.value = description.sdp;
  }

  
  // --------------------------
  // Set offer from create offer
  // ----------------------------
  async function setOffer() {
    const sdp = offerSdpTextarea.value;
    
    const offer = {
      type: 'offer',
      sdp: sdp
    };
    console.log(`Modified Offer from localPeerConnection\n${sdp}`);

    try {
      // eslint-disable-next-line no-unused-vars
      const ignore = await localPeerConnection.setLocalDescription(offer);
      onSetSessionDescriptionSuccess();
    } catch (e) {
      onSetSessionDescriptionError(e);
    }

    try {
      // eslint-disable-next-line no-unused-vars
      const ignore = await remotePeerConnection.setRemoteDescription(offer);
      onSetSessionDescriptionSuccess();
    } catch (e) {
      onSetSessionDescriptionError(e);
    }
  }


  // --------------------------
  // Create Answer this time the remote try to catch the local offer
  // ----------------------------
  async function createAnswer() {
    // Since the 'remote' side has no media stream we need
    // to pass in the right constraints in order for it to
    // accept the incoming offer of audio and video.
    try {
      const answer = await remotePeerConnection.createAnswer();
      gotDescription2(answer);
    } catch (e) {
      onCreateSessionDescriptionError(e);
    }
  }

  function gotDescription2(description) {
    answerSdpTextarea.disabled = false;
    answerSdpTextarea.value = description.sdp;
  }


  // --------------------------
  // Set Answer after remote detect the create answer
  // ----------------------------
  async function setAnswer() {

    const sdp = answerSdpTextarea.value;
    const answer = {
      type: 'answer',
      sdp: sdp
    };

    try {
      // eslint-disable-next-line no-unused-vars
      const ignore = await remotePeerConnection.setLocalDescription(answer);
      onSetSessionDescriptionSuccess();
    } catch (e) {
      onSetSessionDescriptionError(e);
    }

    console.log(`Modified Answer from remotePeerConnection\n${sdp}`);
    try {
      // eslint-disable-next-line no-unused-vars
      const ignore = await localPeerConnection.setRemoteDescription(answer);
      onSetSessionDescriptionSuccess();
    } catch (e) {
      onSetSessionDescriptionError(e);
    }
  }

  // --------------------------
  // End of video call
  // ----------------------------
  function hangup() {

    remoteVideo.srcObject = null;

    console.log('Ending call');

    localStream.getTracks().forEach(track => track.stop());

    sendChannel.close();

    if (receiveChannel) {
      receiveChannel.close();
    }

    localPeerConnection.close();
    remotePeerConnection.close();
    localPeerConnection = null;
    remotePeerConnection = null;
    
    offerSdpTextarea.disabled = true;
    answerSdpTextarea.disabled = true;

    data.getMediaBtn = true;
    data.createPeerConnectionBtn = false;
    data.createOfferBtn = false;
    data.setOfferBtn = false;
    data.createAnswerBtn = false;
    data.setAnswerBtn = false;
    data.hangUpBtn = false;
  }


  return {
    Init: function(){
      Init();
    },
    Data: function(){
      return data;
    },
    Get_Media: function(){
      getMedia();
    },
    Create_Peer_Connection: function(){
      createPeerConnection();
    },
    Create_Offer: function(){
      createOffer();
    },
    Set_Offer: function(){
      setOffer();
    },
    Create_Answer: function(){
      createAnswer();
    },
    Set_Answer: function(){
      setAnswer();
    },
    Hang_up: function(){
      hangup();
    }
  }
});