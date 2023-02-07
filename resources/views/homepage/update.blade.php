<input type="hidden" name="id" value="{{ $vehicles->id }}">

<div class="form-group">
    <label for="">Durum</label>
    <select name="status" id="" class="form-control">
        <option value="">Seçiniz</option>
        <option value="is_done">Teslim Edildi</option>
        <option value="is_arrived">Ulaştı Bekliyor</option>
    </select>
</div>

