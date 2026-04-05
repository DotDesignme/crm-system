<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}?text=Hello%20{{ urlencode($customer->name) }}," 
   target="_blank" 
   class="btn btn-ghost btn-sm" 
   style="justify-content: center; height: 44px; grid-column: span 2; background: rgba(37, 211, 102, 0.08); border: 1px solid rgba(37, 211, 102, 0.2); border-radius: 12px; transition: 0.3s; margin-top: 5px;"
   onmouseover="this.style.background='rgba(37, 211, 102, 0.15)'"
   onmouseout="this.style.background='rgba(37, 211, 102, 0.08)'"
>
    <i class="fab fa-whatsapp" style="margin-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 10px; color: #25D366; font-size: 18px;"></i> 
    <span style="font-weight: 700; color: #fff;">WhatsApp Professional</span>
</a>
