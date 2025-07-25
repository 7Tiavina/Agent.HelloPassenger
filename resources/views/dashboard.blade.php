
  @include('components.header')
  <div class="flex">
    @include('components.sidebar')
    <main id="main-content" class="flex-1 p-6">
      @yield('content')
    </main>
  </div>
</body>
</html>
