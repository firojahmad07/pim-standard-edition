import React from 'react';
import {
  useBackboneRouter,
  useUserCatalogLocale,
} from '../../../../../dependenciesTools/hooks';
import {Attribute, getAttributeLabel} from '../../../../../models';
import {getAttributeByIdentifier} from '../../../../../repositories/AttributeRepository';
import {AkeneoSpinner} from '../../../../../components';

const AttributePreview: React.FC<{attributeCode: string}> = ({
  attributeCode,
}) => {
  const currentCatalogLocale = useUserCatalogLocale();
  const router = useBackboneRouter();
  const [attribute, setAttribute] = React.useState<
    Attribute | null | undefined
  >();

  React.useEffect(() => {
    getAttributeByIdentifier(attributeCode, router).then(attribute =>
      setAttribute(attribute)
    );
  }, [attributeCode]);

  if (undefined === attribute) {
    return <AkeneoSpinner />;
  }

  return (
    <span className='AknRule-attribute'>
      {attribute
        ? getAttributeLabel(attribute, currentCatalogLocale)
        : attributeCode}
    </span>
  );
};

export {AttributePreview};
