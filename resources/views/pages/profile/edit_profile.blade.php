@extends('layouts.app')

@section('css')
<link href="{{ url('css/edit_profile.css') }}" rel="stylesheet">
@endsection

@section('content')
  <section id="edit-profile">
    <script src="{{ asset('js/edit_profile.js') }}" defer></script>
    <article class="update-form">
      <h2>Update Profile Picture</h2>
      <form id="update-photo-form" class="change-information" action="">
        <input type="file" id="imageInput" accept="image/*">
        <img id="imagePreview" style="max-width: 200px; max-height: 200px; display: none;">
        <button id="update_photo_button" type="submit" value="Update Password">Update</button>
      </form>
    </article>

    <article class="update-form">
      <h2>Update Username</h2>
      <form id="update-username-form" class="change-username" action="">
        <input type="text" name="new_username"  placeholder="New Username" >
        <input type="password" name="password" placeholder="Current Password">

        <button id="update-username-button" type="submit" value="Update Username">Update Username</button>
      </form>
    </article>


    <article class="update-form">
      <h2>Update Email</h2>
      <form id="update-email-form" class="change-email" action="../action/action_editProfile/action_edit_email.php" method="post">

        <input type="email" name="new_email" placeholder="New Email" >
        <input type="password" name="password" placeholder="Current Password">

        <button id="update-email-button" type="submit" value="Update Email">Update Email</button>
      </form>
    </article>

    <article class="update-form">
      <h2>Update Password</h2>
      <form id="update-password-form" class="change-password" action="">

        <input type="password" name="new_password" placeholder="New Password">
        <input type="password" name="confirm_password" placeholder="Confirm New Password">
        <input type="password" name="password" placeholder="Current Password">

        <button id="update-password-button" type="submit" value="Update Password">Update Password</button>
      </form>
    </article>

    <article class="update-form">
      <h2>Delete Profile</h2>
      <form id="delete-profile-form" class="delete-profile" action="" method="">

        <input type="password" name="password" placeholder="Current Password">
        <button id="delete-profile-button" type="submit" value="Delete Profile">Delete Profile</button>
      </form>
    </article>
  </section>
@endsection