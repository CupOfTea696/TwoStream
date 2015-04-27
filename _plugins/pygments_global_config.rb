module Jekyll
  module Tags
    class HighlightBlock < Liquid::Block

      def render_pygments(context, code)
        require 'pygments'

        @options[:encoding] = 'utf-8'

        @config = Jekyll.configuration({})

        if @config['pygments_options']
          @config['pygments_options'].each do |opt|
            key, value = opt.split('=')
            if value.nil?
              if key == 'linenos'
                value = 'inline'
              else
                value = true
              end
            end
            @options[key] = value
          end
        end

        output = add_code_tags(
          Pygments.highlight(code, :lexer => @lang, :options => @options),
          @lang
        )

        output = context["pygments_prefix"] + output if context["pygments_prefix"]
        output = output + context["pygments_suffix"] if context["pygments_suffix"]
        output
      end

    end
  end
end

module Jekyll
  module Converters
    class Markdown
      class RedcarpetParser

        module WithPygments
          include CommonMethods
          def block_code(code, lang)
            require 'pygments'
            lang = lang && lang.split.first || "text"

            @options = {}
            @config = Jekyll.configuration({})
            @options[:encoding] = 'utf-8'

            if @config['pygments_options']
              @config['pygments_options'].each do |opt|
                key, value = opt.split('=')
                if value.nil?
                  if key == 'linenos'
                    value = 'inline'
                  else
                    value = true
                  end
                end
                @options[key] = value
              end
            end

            output = add_code_tags(
              Pygments.highlight(code, :lexer => lang, :options => @options),
              lang
            )
          end
        end
      end
    end
  end
end
