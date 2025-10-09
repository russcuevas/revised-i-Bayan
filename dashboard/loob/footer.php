<style>
#openChatBtn:hover {
  transform: scale(1.1);
  transition: 0.2s;
}
</style>

<!-- 💬 Floating Chat Button -->
<button id="openChatBtn" class="btn btn-warning"
    style="position: fixed; bottom: 20px; right: 20px; border-radius: 50%; 
           width: 60px; height: 60px; z-index: 9999; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
    💬
</button>

<!-- 💬 Chat Modal -->
<div class="modal fade" id="chatPopup" tabindex="-1" role="dialog" aria-labelledby="chatPopupLabel">
  <div class="modal-dialog modal-lg" role="document" style="margin-top: 5%;">
    <div class="modal-content" style="border-radius: 15px; overflow: hidden; height: 60vh;">
      
      <div class="modal-header" style="background-color: #B6771D; color: #fff;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="chatPopupLabel">Chat</h4>
      </div>

      <div class="modal-body" id="chatContent" style="height: calc(80vh - 60px); padding: 0; overflow: hidden;">
        <div style="display: flex; justify-content: center; align-items: center; height: 100%;">
          <i class="material-icons" style="font-size: 48px; color: #B6771D;">chat</i>
        </div>
      </div>

    </div>
  </div>
</div>
