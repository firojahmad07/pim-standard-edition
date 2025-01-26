import * as React from 'react';
import {FilterView, FilterViewProps} from 'akeneoreferenceentity/application/configuration/value';
import {ConcreteOptionAttribute} from 'akeneoreferenceentity/domain/model/attribute/type/option';
import {
  NormalizedOption,
  NormalizedOptionCode,
  Option,
} from 'akeneoreferenceentity/domain/model/attribute/type/option/option';
import {EditState} from 'akeneoreferenceentity/application/reducer/reference-entity/edit';
import {connect} from 'react-redux';
import Select2 from 'akeneoreferenceentity/application/component/app/select2';
import __ from 'akeneoreferenceentity/tools/translator';
import {ConcreteOptionCollectionAttribute} from 'akeneoreferenceentity/domain/model/attribute/type/option-collection';
import {getAttributeFilterKey} from 'akeneoreferenceentity/tools/filter';

const memo = (React as any).memo;
const useState = (React as any).useState;

type OptionFilterViewProps = FilterViewProps & {
  context: {
    locale: string;
  };
};

const DEFAULT_OPERATOR = 'IN';

const RIGHT_POSITIONNING_THRESHOLD = 600;

const OptionFilterView: FilterView = memo(({attribute, filter, onFilterUpdated, context}: OptionFilterViewProps) => {
  if (!(attribute instanceof ConcreteOptionAttribute || attribute instanceof ConcreteOptionCollectionAttribute)) {
    return null;
  }

  const [isOpen, setIsOpen] = useState(false);
  const [displayRight, setDisplayRight] = useState(false);

  const availableOptions = attribute
    .getOptions()
    .reduce((availableOptions: {[choiceValue: string]: string}, option: Option) => {
      const normalizedOption: NormalizedOption = option.normalize();
      availableOptions[normalizedOption.code] = option.getLabel(context.locale);

      return availableOptions;
    }, {} as {[label: string]: string});

  const emptyFilter = () => {
    setIsOpen(false);
    onFilterUpdated({
      field: getAttributeFilterKey(attribute),
      operator: DEFAULT_OPERATOR,
      value: [],
      context: {},
    });
  };

  const openPopup = (event: MouseEvent): void => {
    setIsOpen(true);

    if ((event.target as Element).parentElement!.getBoundingClientRect().left < RIGHT_POSITIONNING_THRESHOLD) {
      setDisplayRight(true);
    }
  };

  const value = undefined !== filter ? filter.value : [];
  const labels = value.map((optionCode: NormalizedOptionCode) =>
    undefined !== availableOptions[optionCode] ? availableOptions[optionCode] : `[${optionCode}]`
  );

  return (
    <React.Fragment>
      <span
        className="AknFilterBox-filterLabel"
        onClick={(event: any) => {
          openPopup(event);
        }}
      >
        {attribute.getLabel(context.locale)}
      </span>
      <span
        className="AknFilterBox-filterCriteria AknFilterBox-filterCriteria--limited"
        onClick={(event: any) => {
          openPopup(event);
        }}
      >
        <span className="AknFilterBox-filterCriteriaHint">
          {0 === labels.length ? __('pim_reference_entity.record.grid.filter.option.all') : labels.join(', ')}
        </span>
        <span className="AknFilterBox-filterCaret" />
      </span>
      {isOpen ? (
        <div>
          <div className="AknDropdown-mask" onClick={() => setIsOpen(false)} />
          <div
            className={'AknFilterBox-filterDetails ' + (displayRight ? 'AknFilterBox-filterDetails--rightAlign' : '')}
          >
            <div className="AknFilterChoice">
              <div className="AknFilterChoice-header">
                <div className="AknFilterChoice-title">{attribute.getLabel(context.locale)}</div>
                <div className="AknIconButton AknIconButton--erase" onClick={emptyFilter} />
              </div>
              <div>
                <Select2
                  className="record-option-selector"
                  data={availableOptions}
                  value={value}
                  multiple={true}
                  readOnly={false}
                  configuration={{
                    allowClear: true,
                    placeholder: __('pim_reference_entity.record.grid.filter.option.no_value'),
                    formatSelection: (data: any, _container: any, escapeMarkup: any) => {
                      const result = data ? escapeMarkup(data.text) : undefined;
                      if (result !== undefined) {
                        return '<div title="' + result + '">' + result + '</div>';
                      }

                      return result;
                    },
                    formatResult: (result: any, container: any, _query: any, _escapeMarkup: any) => {
                      const formerResult = '<span class="select2-match"></span>' + result.text;
                      container.attr('title', result.text);
                      return formerResult;
                    },
                  }}
                  onChange={(optionCodes: string[]) => {
                    onFilterUpdated({
                      field: getAttributeFilterKey(attribute),
                      operator: DEFAULT_OPERATOR,
                      value: optionCodes,
                      context: {},
                    });
                  }}
                />
              </div>
            </div>
          </div>
        </div>
      ) : null}
    </React.Fragment>
  );
});

export const filter = connect(
  (state: EditState, ownProps: FilterViewProps): OptionFilterViewProps => {
    return {
      ...ownProps,
      context: {
        locale: state.user.catalogLocale,
      },
    };
  }
)(OptionFilterView);
