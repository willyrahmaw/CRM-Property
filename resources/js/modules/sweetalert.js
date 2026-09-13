import Swal from 'sweetalert2';

const luxurySwal = Swal.mixin({
    customClass: {
        popup: 'rounded-xl border border-[#E8E4DA] shadow-xl',
        title: 'text-[#161616] font-semibold text-lg',
        htmlContainer: 'text-[#79766F] text-sm',
        confirmButton: 'px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-[#161616] hover:bg-[#262626] transition-colors focus:ring-2 focus:ring-[#B89B5E]',
        cancelButton: 'px-5 py-2.5 rounded-lg text-sm font-medium text-[#161616] bg-[#F7F6F2] hover:bg-[#E8E4DA] transition-colors mr-3',
        denyButton: 'px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-[#991B1B] hover:bg-[#7F1D1D] transition-colors',
    },
    buttonsStyling: false,
    background: '#FFFFFF',
});

export function showSuccess(title = 'Berhasil', message = 'Operasi berhasil diproses.') {
    return luxurySwal.fire({
        icon: 'success',
        title,
        text: message,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#161616',
    });
}

export function showError(title = 'Terjadi Kesalahan', message = 'Silakan periksa input atau hubungi administrator.') {
    return luxurySwal.fire({
        icon: 'error',
        title,
        text: message,
        confirmButtonText: 'Mengerti',
    });
}

export function showWarning(title = 'Peringatan', message = '') {
    return luxurySwal.fire({
        icon: 'warning',
        title,
        text: message,
        confirmButtonText: 'Lanjutkan',
    });
}

export function confirmAction({
    title = 'Apakah Anda yakin?',
    text = 'Tindakan ini akan diproses oleh sistem.',
    confirmButtonText = 'Ya, Lanjutkan',
    cancelButtonText = 'Batal',
    isDanger = false,
}) {
    return luxurySwal.fire({
        title,
        text,
        icon: isDanger ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText,
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl border border-[#E8E4DA] shadow-xl',
            title: 'text-[#161616] font-semibold text-lg',
            htmlContainer: 'text-[#79766F] text-sm',
            confirmButton: isDanger
                ? 'px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-[#991B1B] hover:bg-[#7F1D1D] transition-colors'
                : 'px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-[#B89B5E] hover:bg-[#A3884E] transition-colors',
            cancelButton: 'px-5 py-2.5 rounded-lg text-sm font-medium text-[#161616] bg-[#F7F6F2] hover:bg-[#E8E4DA] transition-colors mr-3',
        },
        buttonsStyling: false,
    });
}

export function confirmDelete(title = 'Hapus Data?', text = 'Data yang dihapus tidak dapat dikembalikan.') {
    return confirmAction({
        title,
        text,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        isDanger: true,
    });
}

export function confirmLogout() {
    return luxurySwal.fire({
        title: 'Keluar dari akun?',
        text: 'Anda harus login kembali untuk mengakses sistem.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl border border-[#E8E4DA] shadow-xl',
            title: 'text-[#161616] font-semibold text-lg',
            htmlContainer: 'text-[#79766F] text-sm',
            confirmButton: 'px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-[#991B1B] hover:bg-[#7F1D1D] transition-colors',
            cancelButton: 'px-5 py-2.5 rounded-lg text-sm font-medium text-[#161616] bg-[#F7F6F2] hover:bg-[#E8E4DA] transition-colors mr-3',
        },
        buttonsStyling: false,
    });
}

export default luxurySwal;
