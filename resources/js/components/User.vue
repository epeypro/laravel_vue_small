<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Üye listesi</h3>

                        <div class="card-tools">
                            <button class="btn btn-success" @click="newModal">Yeni Ekle <i class="fas fa-user-plus fa-fw"></i></button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Üye Adı</th>
                                <th>Eposta</th>
                                <th>Rol</th>
                                <th>O.Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{ user.id}}</td>
                                <td>{{ user.name}}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.type | upText }}</td>
                                <td>{{ user.created_at | myDate}}</td>
                                <td>
                                    <a href="#"  @click="editModal(user)"><i class="fa fa-edit green"></i></a>
                                    <a href="#" @click="deleteUser(user.id)"><i class="fa fa-trash red"></i></a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>

        <!--    Ekleme formu başlangıç-->
        <div class="modal fade" id="AddNew" tabindex="-1" role="dialog" aria-labelledby="AddNewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-show="!editmode" id="addNewLabel">Add New</h5>
                        <h5 class="modal-title" v-show="editmode" id="addNewLabel">Update User's Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editmode ? updateUser() : createUser()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>İsim Soyisim</label>
                            <input v-model="form.name" type="text" name="name" autocomplete="off"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Eposta</label>
                            <input v-model="form.email" type="email" name="email" autocomplete="off"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Hakkında</label>
                            <textarea v-model="form.bio" name="bio" id="bio" autocomplete="off"
                                      class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                            <has-error :form="form" field="bio"></has-error>
                        </div>
                        <div class="foprm-group">
                            <select name="type" v-model="form.type" class="form-control" id="type" :class="{ 'is-invalid': form.errors.has('bio') }">
                                <option value="">Üye Rolünü Seç</option>
                                <option value="admin">Admin</option>
                                <option value="user">Kullanıcı</option>
                                <option value="author">Yazar</option>
                            </select>
                            <has-error :form="form" field="type"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Şifre</label>
                            <input v-model="form.password" type="password" name="password" autocomplete="off"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                            <has-error :form="form" field="password"></has-error>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                    </div>
                    </form>



                </div>
            </div>
        </div>
        <!--    başlangıç nokta bitiş-->

    </div>


</template>



<script>
    export default {

        data(){
            return {
                editmode: false,
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
                })
            }
        },
        methods: {
            updateUser() {
                this.$Progress.start();
                this.form.put('api/user/'+this.form.id)
                .then(() => {
                    $('#AddNew').modal('hide');
                    Swal.fire(
                        'Tebrikler!',
                        'Üye başarlı bir şekilde güncellendi.',
                        'success'
                        )
                    this.loadUsers();
                    this.$Progress.finish();

                })
                .catch(() => {
                    this.$Progress.fail();
                })
            },
            editModal(user) {
                this.editmode = true;
                this.form.reset();
                $('#AddNew').modal('show');
                this.form.fill(user);
            },
            newModal() {
                this.editmode = false;
                this.form.reset();
                $('#AddNew').modal('show');
            },

            deleteUser(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {


                    if (result.value) {
                        this.form.delete('api/user/' + id).then(() => {
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            )
                            this.loadUsers();
                        }).catch(() => {
                            Swal.fire(
                                'Haydaaaa!',
                                'Bir hata oldu. Silinemedi.',
                                'warning'
                            );
                        });
                    }})
            },

            loadUsers(){
                axios.get("api/user").then(({ data }) =>( this.users = data.data ));
            },

            createUser(){
                this.$Progress.start();
                this.form.post('/api/user')
                    .then(() => {
                        Fire.$emit('AfterCreate');

                        $('#AddNew').modal('hide');

                        Toast.fire({
                            icon: 'success',
                            title: 'Üye başarlı bir şekilde eklendi'
                        })

                        this.$Progress.finish();
                    })

                    .catch(() => {
                        console.log("Error......")
                    })
            }
        },
        created() {
            this.loadUsers();
            // setInterval(() => this.loadUsers(),3000);

            Fire.$on('AfterCreate',()=> {
                this.loadUsers();
            });
        }
    }
</script>
