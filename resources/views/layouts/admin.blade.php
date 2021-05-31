<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- admin.css here -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet" />

    <!-- app js values -->
    <script type="application/javascript">
        var LSK_APP = {};
        LSK_APP.APP_URL = "{{ env('APP_URL') }}";
        LSK_APP.AUTH_USER = @json(auth()->user());

    </script>
</head>

<body>
    <div id="admin" class="v-application--wrap">
        <template>
            <v-app id="inspire">
                {{-- <v-navigation-drawer v-model="drawer" expand-on-hover clipped app left dense>
                    <v-list dense>
                        @foreach ($nav as $n)
                            @if ($n->navType == \App\Components\Core\Menu\MenuItem::$NAV_TYPE_NAV && $n->visible)
                                <v-list-item :to="{name: '{{ $n->routeName }}'}" :exact="false" dense>
                                    <v-list-item-action>
                                        <v-icon>{{ $n->icon }}</v-icon>
                                    </v-list-item-action>
                                    <v-list-item-content>
                                        <v-list-item-title>
                                            {{ $n->label }}
                                        </v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            @else
                                <v-divider></v-divider>
                            @endif
                        @endforeach

                        <v-list-item @click="clickLogout('{{ route('logout') }}','{{ url('/') }}')">
                            <v-list-item-action>
                                <v-icon>mdi-logout</v-icon>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>Logout</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </v-navigation-drawer> --}}

                <the-layout-drawer :value="drawer" :nav="{{ $nav }}"></the-layout-drawer>

                <v-app-bar app dense>
                    <v-app-bar-nav-icon @click.stop="drawer = !drawer">
                        <v-icon>
                            @{{ drawer ? 'mdi-format-indent-decrease' : 'mdi-format-indent-increase' }}
                        </v-icon>
                    </v-app-bar-nav-icon>
                    {{-- <v-toolbar-title>{{ config("app.name") }}</v-toolbar-title> --}}
                    <v-breadcrumbs :items="getBreadcrumbs" class="overline d-inline-block text-truncate">
                        <template v-slot:divider>
                            <v-icon>mdi-forward</v-icon>
                        </template>
                        <template v-slot:item="props">
                            <v-breadcrumbs-item :to="props.item.to" exact :key="props.item.label"
                                :disabled="props.item.disabled">
                                @{{ props . item . label }}
                            </v-breadcrumbs-item>
                        </template>
                    </v-breadcrumbs>
                    <v-spacer></v-spacer>
                    <notification></notification>
                    <v-menu offset-x offset-y open-on-hover transition="slide-y-transition">
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn v-bind="attrs" v-on="on" text>
                                {{ auth()->user()->name }}
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item @click="clickLogout('{{ route('logout') }}','{{ url('/') }}')">
                                <v-list-item-action>
                                    <v-icon>mdi-logout</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>Cerra Session</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </v-app-bar>

                <v-main>
                    <v-slide-x-transition>
                        <router-view></router-view>
                    </v-slide-x-transition>
                    <portal-target name="modal" multiple></portal-target>
                </v-main>
                <v-footer v-if="!!!$vuetify.breakpoint.mobile" app fixed dark dense>
                    <v-spacer />
                    <div class="overline text-right"> Equipos y Tractores del Bajio &copy;
                        {{ date('Y') }}
                    </div>
                </v-footer>

                <!-- snackbar -->
                <v-snackbar app v-model="showSnackbar" :timeout="snackbarDuration" :color="snackbarColor" top right>
                    <span class="overline">
                        @{{ snackbarMessage }}
                    </span>
                </v-snackbar>

            </v-app>

            <!-- loader -->
            <div v-if="showLoader" class="wask_loader bg_half_transparent">
                <moon-loader color="green"></moon-loader>
            </div>

            <!-- dialog confirm -->
            <v-dialog v-show="showDialog" v-model="showDialog" absolute max-width="450px">
                <v-card>
                    <v-card-title class="headline grey lighten-2 text-uppercase">
                        <v-icon v-if="dialogIcon">@{{ dialogIcon }}</v-icon>
                        @{{ dialogTitle }}
                    </v-card-title>
                    <v-card-text class="body-1 pt-3">@{{ dialogMessage }}</v-card-text>
                    <v-card-actions v-if="dialogType=='confirm'">
                        <v-spacer></v-spacer>
                        <v-btn color="error lighten-1" @click.native="dialogCancel">NO</v-btn>
                        <v-btn color="success darken-1" @click.native="dialogOk">SI</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- the progress bar -->
            <vue-progress-bar></vue-progress-bar>
        </template>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>

</html>
