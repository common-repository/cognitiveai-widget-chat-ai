<?php
/**
 * Plugin Name: Cognitiveai widget chat AI
 * Description: Чат с нейронными сетями, GPT-3.5, GPT-4, YandexGPT, DALL-E и другими.
 * Version: 1.00
 * Author: R.Bond
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cognitiveai-widget-chat-ai
 */

// Если файл вызывается напрямую, прекратить выполнение.
if (!defined('ABSPATH')) {
	exit;
}

// Создание страницы настроек в админ-панели
function cognitiveai_chat_widget_settings_page() {
	add_options_page(
		'Чат с нейросетями',
		'Чат с нейросетями',
		'manage_options',
		'cognitiveai-widget-chat-ai.php',
		'cognitiveai_chat_widget_settings_page_html'
	);
}
add_action('admin_menu', 'cognitiveai_chat_widget_settings_page');

register_uninstall_hook(__FILE__, 'cognitiveai_chat_widget_uninstall');
function cognitiveai_chat_widget_uninstall() {
	delete_option('cognitiveai_chat_widget_id');
	delete_option('cognitiveai_chat_widget_enabled');
}

// HTML содержимое страницы настроек
function cognitiveai_chat_widget_settings_page_html() {
	if (!current_user_can('manage_options')) {
		return;
	}

	if (isset($_POST['cognitiveai_chat_widget_id'])) {
		check_admin_referer('cognitiveai_chat_widget_save_settings');
		update_option('cognitiveai_chat_widget_id', sanitize_text_field(trim($_POST['cognitiveai_chat_widget_id'])));
		$widget_enabled = isset($_POST['cognitiveai_chat_widget_enabled']) && $_POST['cognitiveai_chat_widget_enabled'] === 'on' ? 'yes' : 'no';
		update_option('cognitiveai_chat_widget_enabled', $widget_enabled);
		?>
		<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
				<p><strong>Настройки сохранены.</strong></p>
		</div>
		<?php
	}

	// Получение текущих настроек
	$widget_id = get_option('cognitiveai_chat_widget_id', '');
	$widget_enabled = get_option('cognitiveai_chat_widget_enabled', 'no');
	?>
	<div class="wrap">
		<h1>Настройки плагина "Чат с нейросетями"</h1>
		<form method="post" action="">
			<?php wp_nonce_field('cognitiveai_chat_widget_save_settings'); ?>
			<table class="form-table">
				<tr>
					<th scope="row">Выкл/вкл</th>
					<td>
						<input type="checkbox" name="cognitiveai_chat_widget_enabled" <?php checked($widget_enabled, 'yes'); ?>>
					</td>
				</tr>
				<tr>
					<th scope="row">ID виджета</th>
					<td>
						<input type="text" name="cognitiveai_chat_widget_id" value="<?php echo esc_attr($widget_id); ?>" class="regular-text">
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

// Добавление JavaScript на страницы сайта или в админ-панель для администраторов
function cognitiveai_chat_widget_add_script() {
    $widget_id = get_option('cognitiveai_chat_widget_id', '');
    $widget_enabled = get_option('cognitiveai_chat_widget_enabled', 'no');
    if ('yes' === $widget_enabled && !empty($widget_id)) {
			if (is_admin() || (is_user_logged_in() && current_user_can('administrator'))) {
				echo '<script id="cognitiveaiChatWidgetId" src="https://widget.cognitiveai.ru/chat/widgets.js?widgetId=' . esc_attr($widget_id) . '" async></script>';
			}
    }
}

add_action('wp_footer', 'cognitiveai_chat_widget_add_script');
add_action('admin_footer', 'cognitiveai_chat_widget_add_script');

// Функция для добавления ссылки на настройки в списке плагинов
function cognitiveai_chat_widget_add_settings_link($links) {
	return array_merge(array('settings' => '<a href="options-general.php?page=cognitiveai-widget-chat-ai.php">' . __('Настройки', 'cognitiveai-widget-chat-ai') . '</a>'), $links);
}

// Добавляем фильтр для добавления ссылки на настройки
add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'cognitiveai_chat_widget_add_settings_link');