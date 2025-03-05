@props(['label'])

<div class="filter-item">
  <div class="filter-label">{{ $label }}</div>
  <div class="filter-content">
    {{ $slot }}
  </div>
</div>
