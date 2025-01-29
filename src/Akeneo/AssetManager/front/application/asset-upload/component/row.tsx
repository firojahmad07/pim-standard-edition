import React from 'react';
import styled, {css} from 'styled-components';
import {CloseIcon, RefreshIcon, DangerIcon, getColor, IconButton} from '@akeneo-pim-community/akeneo-design-system';
import {useTranslate, LocaleCode, getLocalesFromChannel} from '@akeneo-pim-community/shared';
import Line, {LineStatus} from 'akeneoassetmanager/application/asset-upload/model/line';
import RowStatus from 'akeneoassetmanager/application/asset-upload/component/row-status';
import {getAllErrorsOfLineByTarget, getStatusFromLine} from 'akeneoassetmanager/application/asset-upload/utils/utils';
import Spacer from 'akeneoassetmanager/application/component/app/spacer';
import {ColumnWidths} from 'akeneoassetmanager/application/asset-upload/component/line-list';
import Channel from 'akeneoassetmanager/domain/model/channel';
import {ChannelDropdown} from 'akeneoassetmanager/application/component/app/ChannelDropdown';
import {LocaleDropdown} from 'akeneoassetmanager/application/component/app/LocaleDropdown';

const Container = styled.div<{status?: LineStatus; isReadOnly?: boolean}>`
  border-bottom: 1px solid ${getColor('grey', 80)};
  padding: 15px 0;

  ${props =>
    props.status === LineStatus.Invalid &&
    css`
      background: ${getColor('red', 20)};
      border-bottom: 1px solid #ffffff;
    `}

  ${props =>
    props.isReadOnly === true &&
    css`
      opacity: 0.3;
      user-select: none;
    `}
`;

const Cells = styled.div`
  display: flex;
  justify-content: space-between;
  align-items: center;
`;

const Cell = styled.div<{width?: number}>`
  color: ${getColor('grey', 140)};
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  flex-grow: 0;
  flex-shrink: 0;
  padding: 0 15px;

  ${props =>
    props.width !== undefined &&
    css`
      width: ${props.width}px;
    `}
`;

const ActionsCell = styled(Cell)`
  flex-direction: row;
  justify-content: flex-end;
  gap: 10px;
`;

const StyledFilename = styled.div`
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  word-break: break-all;
`;

const Thumbnail = styled.img`
  height: 48px;
  object-fit: cover;
  width: 48px;
  border: 1px solid ${getColor('grey', 80)};
`;

const Input = styled.input<{readOnly?: boolean; isValid?: boolean}>`
  border-radius: 2px;
  border: 1px solid ${getColor('grey', 80)};
  height: 40px;
  line-height: 40px;
  padding: 0 5px 0 15px;
  width: 220px;

  ${props =>
    props.isValid === false &&
    css`
      border-color: ${getColor('red', 100)};
    `}
`;

const Errors = styled.div`
  align-items: flex-start;
  display: flex;
  justify-content: space-between;
`;

const StyledError = styled.div`
  align-items: center;
  color: ${getColor('red', 100)};
  font-size: 11px;
  line-height: 16px;
  margin: 5px 0 0;
  overflow-wrap: break-word;
  padding: 0;

  svg {
    float: left;
    margin: 0 5px 0 0;
  }
`;

const Error = ({message, ...props}: {message: string} & any) => (
  <StyledError {...props} aria-label={message}>
    <DangerIcon size={16} />
    {message}
  </StyledError>
);

type RowProps = {
  line: Line;
  locale: LocaleCode;
  channels: Channel[];
  onLineRemove: (line: Line) => void;
  onLineChange: (line: Line) => void;
  onLineUploadRetry: (line: Line) => void;
  valuePerLocale: boolean;
  valuePerChannel: boolean;
};

const Row = React.memo(
  ({
    line,
    locale,
    channels,
    onLineRemove,
    onLineChange,
    onLineUploadRetry,
    valuePerLocale,
    valuePerChannel,
  }: RowProps) => {
    const translate = useTranslate();
    const status = getStatusFromLine(line, valuePerLocale, valuePerChannel);
    const errors = getAllErrorsOfLineByTarget(line);
    const isReadOnly = line.isAssetCreating || line.assetCreated;

    const handleCodeChange = React.useCallback(
      (event: React.ChangeEvent<HTMLInputElement>) => {
        onLineChange({...line, code: event.target.value});
      },
      [line]
    );

    const handleChannelChange = React.useCallback(
      (value: string) => {
        onLineChange({...line, channel: value ? value : null});
      },
      [line]
    );

    const handleLocaleChange = React.useCallback(
      (value: string) => {
        onLineChange({...line, locale: value ? value : null});
      },
      [line]
    );

    const handleRetryUpload = React.useCallback(() => {
      onLineUploadRetry(line);
    }, [onLineUploadRetry, line]);

    const handleLineRemove = React.useCallback(() => {
      onLineRemove(line);
    }, [line, onLineRemove]);

    return (
      <Container status={status} isReadOnly={isReadOnly}>
        <Cells>
          <Cell width={ColumnWidths.asset}>{null !== line.thumbnail && <Thumbnail src={line.thumbnail} alt="" />}</Cell>
          <Cell width={ColumnWidths.filename}>
            <StyledFilename title={line.filename}>{line.filename}</StyledFilename>
          </Cell>
          <Cell width={ColumnWidths.code}>
            <Input
              type="text"
              value={line.code}
              isValid={errors.code.length === 0}
              disabled={isReadOnly}
              onChange={handleCodeChange}
              aria-label={translate('pim_asset_manager.asset.upload.list.code')}
            />
          </Cell>
          {valuePerChannel && (
            <Cell width={ColumnWidths.channel}>
              <ChannelDropdown
                channel={line.channel}
                channels={channels}
                uiLocale={locale}
                readOnly={isReadOnly}
                onChange={handleChannelChange}
              />
            </Cell>
          )}
          {valuePerLocale && (
            <Cell width={ColumnWidths.locale}>
              <LocaleDropdown
                locale={line.locale}
                locales={getLocalesFromChannel(channels, line.channel)}
                readOnly={isReadOnly}
                onChange={handleLocaleChange}
              />
            </Cell>
          )}
          <Spacer />
          <Cell width={ColumnWidths.status}>
            <RowStatus status={status} progress={line.uploadProgress} />
          </Cell>
          <ActionsCell width={ColumnWidths.actions}>
            {!isReadOnly && line.isFileUploadFailed && (
              <IconButton
                icon={<RefreshIcon />}
                level={LineStatus.Invalid === status ? 'danger' : 'tertiary'}
                ghost="borderless"
                onClick={handleRetryUpload}
                title={translate('pim_asset_manager.asset.upload.retry')}
              />
            )}
            {!isReadOnly && (
              <IconButton
                icon={<CloseIcon />}
                level={LineStatus.Invalid === status ? 'danger' : 'tertiary'}
                ghost="borderless"
                onClick={handleLineRemove}
                title={translate('pim_asset_manager.asset.upload.remove')}
              />
            )}
          </ActionsCell>
        </Cells>
        <Errors>
          <Cell width={ColumnWidths.asset + ColumnWidths.filename}>
            {errors.common.map(error => (
              <Error key={error.message} message={translate(error.messageTemplate, error.parameters)} />
            ))}
          </Cell>
          <Cell width={ColumnWidths.code}>
            {errors.code.map(error => (
              <Error key={error.message} message={translate(error.messageTemplate, error.parameters)} />
            ))}
          </Cell>
          <Cell width={ColumnWidths.channel}>
            {errors.channel.map(error => (
              <Error key={error.message} message={translate(error.messageTemplate, error.parameters)} />
            ))}
          </Cell>
          <Cell width={ColumnWidths.locale}>
            {errors.locale.map(error => (
              <Error key={error.message} message={translate(error.messageTemplate, error.parameters)} />
            ))}
          </Cell>
          <Spacer />
        </Errors>
      </Container>
    );
  }
);

export default Row;
