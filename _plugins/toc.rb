require 'nokogiri'

module Jekyll
  module TOCGenerator
    TOC_CONTAINER_HTML = '<ul class="toc">%1</ul>'

   def toc_generate(html)
        doc = Nokogiri::HTML(html)
        doc.encoding = 'UTF-8'
       
        toc_placeholder = doc.xpath("//comment()[contains(.,'TOC')]")
       
        if toc_placeholder
            if toc_placeholder.count == 0
                return html
            end
        else
            #return html
        end

        config = @context.registers[:site].config
        # Minimum number of items needed to show TOC, default 0 (0 means no minimum)
        min_items_to_show_toc = config["minItemsToShowToc"] || 0

        # Text labels
        hide_label = config["hideLabel"] || 'hide'
        show_label = config["showLabel"] || 'show'
        show_toggle_button = config["showToggleButton"]

        toc_html = ''
        toc_level = 1
        item_number = 1
        level_html = ''

        # Find H1 tag and all its H2 siblings until next H1
        doc.css('h2').each do |h2|
            # TODO This XPATH expression can greatly improved
            ct  = h2.xpath('count(following-sibling::h2)')
            h3s = h2.xpath("following-sibling::h3[count(following-sibling::h2)=#{ct}]")

            level_html = '';
            inner_section = 0;

            h3s.map.each do |h3|
                inner_section += 1;

                level_html += create_level_html(h3['id'],
                    toc_level + 1,
                    item_number.to_s + '.' + inner_section.to_s,
                    h3.text,
                    '')
            end
            if level_html.length > 0
                level_html = '<ul class="toc/level toc">' + level_html + '</ul>';
            end

            toc_html += create_level_html(h2['id'],
                toc_level,
                item_number,
                h2.text,
                level_html);

            item_number += 1;
        end

        # for convenience item_number starts from 1
        # so we decrement it to obtain the index count
        toc_index_count = item_number - 1
       
        if toc_html.length > 0
            if min_items_to_show_toc <= toc_index_count
                toc_table = TOC_CONTAINER_HTML
                    .gsub('%1', toc_html);
                toc_placeholder.each do |placeholder|
                    placeholder.replace toc_table
                end
            end
            doc.css('body').children.to_html
        else
            return html
        end
   end

private

    def create_level_html(anchor_id, toc_level, tocNumber, tocText, tocInner)
        link = '<a data-scrollto="%1">%2</a>%3'
            .gsub('%1', anchor_id.to_s)
            .gsub('%2', tocText)
            .gsub('%3', tocInner ? tocInner : '');
        '<li class="toc/item">%2</li>'
            .gsub('%1', toc_level.to_s)
            .gsub('%2', link)
    end
  end
end

Liquid::Template.register_filter(Jekyll::TOCGenerator)
